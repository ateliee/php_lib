/*
 * KAKASI (Kanji Kana Simple inversion program)
 * $Id: mkkanwa.c,v 1.4 2001/01/16 07:51:47 rug Exp $
 * Copyright (C) 1992
 * Hironobu Takahashi (takahasi@tiny.or.jp)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either versions 2, or (at your option)
 * any later version.
 *
 * This program is distributed in the hope that it will be useful
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with KAKASI, see the file COPYING.  If not, write to the Free
 * Software Foundation Inc., 59 Temple Place - Suite 330, Boston, MA
 * 02111-1307, USA.
 */

#ifdef HAVE_CONFIG_H
# include <config.h>
#endif

#include <stdio.h>
#ifdef HAVE_STRING_H
# include <string.h>
#else
# include <strings.h>
#endif
#include <stdlib.h>
#include "getopt.h"
#include "kakasi.h"

static void
usage(argv)
     char **argv;
{
    (void)fprintf(stderr, "usage: %s kanwadict dict1 [dict2,,,]\n", argv[0]);
    exit (2);
}

static void
make_kanwa_dict(kdict)
     FILE *kdict;
{
    unsigned char length;
    int i, j, count;
    struct kanji_yomi *ptr;

    fseek(kdict, sizeof kanwa, 0L);

    for (i = 0x20; i < 0x7f; ++ i)
	for (j = 0x20; j < 0x7f; ++ j) {
	    kanwa[i-0x20][j-0x20].index = ftell(kdict);
	    count = 0;
	    for (ptr = jisyo_table[i][j]; ptr != NULL;
		 ptr = ptr->next) {
		fwrite(&(ptr->tail), 1, 1, kdict);
		length = ptr->length - ((ptr->tail == 0) ? 2 : 3);
		fwrite(&length, 1, 1, kdict);
		fwrite(ptr->kanji, (int)length, 1, kdict);
		length = strlen((const char*)ptr->yomi);
		fwrite(&length, 1, 1, kdict);
		fwrite(ptr->yomi, (int)length, 1, kdict);
		++count;
	    }
	    kanwa[i-0x20][j-0x20].entry = count;
	}

    fseek(kdict, 0, 0L);
    fwrite((char *)kanwa, sizeof kanwa, 1, kdict);
}

int
main(argc, argv)
     int argc;
     char **argv;
{
    FILE *kdict;
    static char options[]="h";
    int c;
    extern int optind;

    /* An optional extraction. = It must not be this version. */
    while ((c = getopt(argc, argv, options)) != -1) {
	switch (c) {
	  case 'h':
	  default:
	    usage(argv, options);
	}
    }
    if (optind >= argc)
	usage(argv);
    if ((kdict = fopen(argv[optind],"wb")) == NULL) {
	perror(argv[optind]);
	exit(2);
    }

    /* The preparation of the change table of ITAIJI. */
    mkitaijitbl();

    /* Reading of the dictionary. */
    init_jisyo();
    for (optind ++ ; optind < argc; optind++)
	add_jisyo(argv[optind]);

    make_kanwa_dict(kdict);
    fclose(kdict);
    return 0;
}
