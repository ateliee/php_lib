/*
 * KAKASI (Kanji Kana Simple inversion program)
 * $Id: k2.c,v 1.3 2001/01/16 07:51:47 rug Exp $
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
#include "kakasi.h"

struct k2rom_tbl {
    char kana[5];
    char romaji[7];
}
k2rom_h_table[] = { 
    " ", " ", "!", ".", "\"", "(", "#", ")", "$", ",", "%", ".", "&",
    "wo","'", "a","(", "i",")", "u","*", "e","+", "o",",", "ya","-",
    "yu",".", "yo","/", "tu","/3^", "vvu","/3^'", "vva","/3^(",
    "vvi","/3^*", "vve","/3^+", "vvo","/6", "kka","/6^", "gga","/7",
    "kki","/7,", "kkya","/7-", "kkyu","/7.", "kkyo","/7^", "ggi","/7^,",
    "ggya","/7^-", "ggyu","/7^.", "ggyo","/8", "kku","/8^", "ggu","/9",
    "kke","/9^", "gge","/:", "kko","/:^", "ggo","/;", "ssa","/;^",
    "zza","/<", "sshi","/<,", "ssha","/<-", "sshu","/<.", "ssho","/<^",
    "jji","/<^,", "jja","/<^-", "jju","/<^.", "jjo","/=", "ssu","/=^",
    "zzu","/>", "sse","/>^", "zze","/?", "sso","/?^", "zzo","/@",
    "tta","/@^", "dda","/A", "cchi","/A,", "ccha","/A-", "cchu","/A.",
    "ccho","/A^", "ddi","/A^,", "ddya","/A^-", "ddyu","/A^.", "ddyo","/B",
    "ttsu","/B^", "ddu","/C", "tte","/C^", "dde","/D", "tto","/D^",
    "ddo","/J", "hha","/J^", "bba","/J_", "ppa","/K", "hhi","/K,",
    "hhya","/K-", "hhyu","/K.", "hhyo","/K^", "bbi","/K^,", "bbya","/K^-",
    "bbyu","/K^.", "bbyo","/K_", "ppi","/K_,", "ppya","/K_-",
    "ppyu","/K_.", "ppyo","/L", "ffu","/L'", "ffa","/L(", "ffi","/L*",
    "ffe","/L+", "ffo","/L^", "bbu","/L_", "ppu","/M", "hhe","/M^",
    "bbe","/M_", "ppe","/N", "hho","/N^", "bbo","/N_", "ppo","/T",
    "yya","/U", "yyu","/V", "yyo","/W", "rra","/X", "rri","/X,",
    "rrya","/X-", "rryu","/X.", "rryo","/Y", "rru","/Z", "rre","/[",
    "rro","0", "^","1", "a","2", "i","3", "u","3^", "vu","3^'",
    "va","3^(", "vi","3^*", "ve","3^+", "vo","4", "e","5", "o","6",
    "ka","6^", "ga","7", "ki","7,", "kya","7-", "kyu","7.", "kyo","7^",
    "gi","7^,", "gya","7^-", "gyu","7^.", "gyo","8", "ku","8^", "gu","9",
    "ke","9^", "ge",":", "ko",":^", "go",";", "sa",";^", "za","<",
    "shi","<,", "sha","<-", "shu","<.", "sho","<^", "ji","<^,",
    "ja","<^-", "ju","<^.", "jo","=", "su","=^", "zu",">", "se",">^",
    "ze","?", "so","?^", "zo","@", "ta","@^", "da","A", "chi","A,",
    "cha","A-", "chu","A.", "cho","A^", "di","A^,", "dya","A^-",
    "dyu","A^.", "dyo","B", "tsu","B^", "du","C", "te","C^", "de","D",
    "to","D^", "do","E", "na","F", "ni","F,", "nya","F-", "nyu","F.",
    "nyo","G", "nu","H", "ne","I", "no","J", "ha","J^", "ba","J_",
    "pa","K", "hi","K,", "hya","K-", "hyu","K.", "hyo","K^", "bi","K^,",
    "bya","K^-", "byu","K^.", "byo","K_", "pi","K_,", "pya","K_-",
    "pyu","K_.", "pyo","L", "fu","L'", "fa","L(", "fi","L*", "fe","L+",
    "fo","L^", "bu","L_", "pu","M", "he","M^", "be","M_", "pe","N",
    "ho","N^", "bo","N_", "po","O", "ma","P", "mi","P,", "mya","P-",
    "myu","P.", "myo","Q", "mu","R", "me","S", "mo","T", "ya","U",
    "yu","V", "yo","W", "ra","X", "ri","X,", "rya","X-", "ryu","X.",
    "ryo","Y", "ru","Z", "re","[", "ro","\\", "wa","]", "n",
    "]1", "n'a", "]2", "n'i", "]3", "n'u", "]4", "n'e", "]5", "n'o",
    "^", "\"","_", "(maru)", "", ""
}, k2rom_k_table[] = { 
    " ", " ", "!", ".", "\"", "(", "#", ")", "$", ",", "%", ".", "&",
    "wo", "'", "a", "(", "i", ")", "u", "*", "e", "+", "o", ",", "ya",
    "-", "yu", ".", "yo", "/", "tsu", "/3^", "vvu", "/3^'", "vva", "/3^(",
    "vvi", "/3^*", "vve", "/3^+", "vvo", "/6", "kka", "/6^", "gga", "/7",
    "kki", "/7,", "kkya", "/7-", "kkyu", "/7.", "kkyo", "/7^", "ggi",
    "/7^,", "ggya", "/7^-", "ggyu", "/7^.", "ggyo", "/8", "kku", "/8^",
    "ggu", "/9", "kke", "/9^", "gge", "/:", "kko", "/:^", "ggo", "/;",
    "ssa", "/;^", "zza", "/<", "ssi", "/<,", "ssya", "/<-", "ssyu", "/<.",
    "ssyo", "/<^", "zzi", "/<^,", "zzya", "/<^-", "zzyu", "/<^.", "zzyo",
    "/=", "ssu", "/=^", "zzu", "/>", "sse", "/>^", "zze", "/?", "sso",
    "/?^", "zzo", "/@", "tta", "/@^", "dda", "/A", "tti", "/A,", "ttya",
    "/A-", "ttyu", "/A.", "ttyo", "/A^", "ddi", "/A^,", "ddya", "/A^-",
    "ddyu", "/A^.", "ddyo", "/B", "ttu", "/B^", "ddu", "/C", "tte", "/C^",
    "dde", "/D", "tto", "/D^", "ddo", "/J", "hha", "/J^", "bba", "/J_",
    "ppa", "/K", "hhi", "/K,", "hhya", "/K-", "hhyu", "/K.", "hhyo",
    "/K^", "bbi", "/K^,", "bbya", "/K^-", "bbyu", "/K^.", "bbyo", "/K_",
    "ppi", "/K_,", "ppya", "/K_-", "ppyu", "/K_.", "ppyo", "/L", "hhu",
    "/L'", "ffa", "/L(", "ffi", "/L*", "ffe", "/L+", "ffo", "/L^", "bbu",
    "/L_", "ppu", "/M", "hhe", "/M^", "bbe", "/M_", "ppe", "/N", "hho",
    "/N^", "bbo", "/N_", "ppo", "/T", "yya", "/U", "yyu", "/V", "yyo",
    "/W", "rra", "/X", "rri", "/X,", "rrya", "/X-", "rryu", "/X.", "rryo",
    "/Y", "rru", "/Z", "rre", "/[", "rro", "0", "^", "1", "a", "2", "i",
    "3", "u", "3^", "vu", "3^'", "va", "3^(", "vi", "3^*", "ve", "3^+",
    "vo", "4", "e", "5", "o", "6", "ka", "6^", "ga", "7", "ki", "7,",
    "kya", "7-", "kyu", "7.", "kyo", "7^", "gi", "7^,", "gya", "7^-",
    "gyu", "7^.", "gyo", "8", "ku", "8^", "gu", "9", "ke", "9^", "ge",
    ":", "ko", ":^", "go", ";", "sa", ";^", "za", "<", "si", "<,", "sya",
    "<-", "syu", "<.", "syo", "<^", "zi", "<^,", "zya", "<^-", "zyu",
    "<^.", "zyo", "=", "su", "=^", "zu", ">", "se", ">^", "ze", "?", "so",
    "?^", "zo", "@", "ta", "@^", "da", "A", "ti", "A,", "tya", "A-",
    "tyu", "A.", "tyo", "A^", "di", "A^,", "dya", "A^-", "dyu", "A^.",
    "dyo", "B", "tu", "B^", "du", "C", "te", "C^", "de", "D", "to", "D^",
    "do", "E", "na", "F", "ni", "F,", "nya", "F-", "nyu", "F.", "nyo",
    "G", "nu", "H", "ne", "I", "no", "J", "ha", "J^", "ba", "J_", "pa",
    "K", "hi", "K,", "hya", "K-", "hyu", "K.", "hyo", "K^", "bi", "K^,",
    "bya", "K^-", "byu", "K^.", "byo", "K_", "pi", "K_,", "pya", "K_-",
    "pyu", "K_.", "pyo", "L", "hu", "L'", "fa", "L(", "fi", "L*", "fe",
    "L+", "fo", "L^", "bu", "L_", "pu", "M", "he", "M^", "be", "M_", "pe",
    "N", "ho", "N^", "bo", "N_", "po", "O", "ma", "P", "mi", "P,", "mya",
    "P-", "myu", "P.", "myo", "Q", "mu", "R", "me", "S", "mo", "T", "ya",
    "U", "yu", "V", "yo", "W", "ra", "X", "ri", "X,", "rya", "X-", "ryu",
    "X.", "ryo", "Y", "ru", "Z", "re", "[", "ro", "\\", "wa", "]", "n",
    "]1", "n'a", "]2", "n'i", "]3", "n'u", "]4", "n'e", "]5", "n'o",
    "^", "\"", "_", "(maru)", "", ""};


#define k2rom_buflen 10

static int
k2rom(c, n, type)
     Character *c;
     Character *n;
     int type;
{
    static int index_table[0x41];
    static int index_made=0;
    static struct k2rom_tbl *k2rom_ptr;
    struct k2rom_tbl *p;
    int i, clen, ylen;
    char buffer[k2rom_buflen];
    unsigned char c1;
    int max_match, match_more;
    char *max_romaji;

    if (index_made == 0) {
	k2rom_ptr = (romaji_type == HEPBURN) ? k2rom_h_table : k2rom_k_table;
	index_table[0] = 0;
	for (p = k2rom_ptr, i = 0; *(p->kana) != '\0'; ++ p, ++ i) {
	    index_table[*(p->kana)-0x20+1] = i+1;
	}
	index_made = 1;
    }

    buffer[k2rom_buflen] = '\0'; clen = k2rom_buflen;
    for (i = 0; i < k2rom_buflen; ++ i) {
	c1 = c[i].c1;
	if ((0 < c1) && (c1 < 0x20))
	    buffer[i] = ' ';
	else if (0x60 < c1)
	    buffer[i] = ' ';
	else
	    buffer[i] = c1;

	if (c1 == '\0') {
	    clen = i;
	    break;
	}
    }

    if (clen == 0) {
	n[0].type=OTHER;
	n[0].c1 = '\0';
	return 0;
    }

    max_match = 0;
    max_romaji = NULL;
    match_more = 0;
    for (i = index_table[buffer[0]-0x20];
	 i < index_table[buffer[0]-0x20+1];
	 ++ i) {
	ylen = strlen(k2rom_ptr[i].kana);
	if (clen >= ylen) {
	    if (max_match < ylen) {
		if (strncmp(buffer, k2rom_ptr[i].kana, ylen) == 0) {
		    max_match = ylen;
		    max_romaji = k2rom_ptr[i].romaji;
		}
	    }
	} else {
	    if (match_more == 0)
		if (strncmp(buffer, k2rom_ptr[i].kana, clen) == 0)
		    match_more = 1;
	}
    }

    for (i = 0; max_romaji[i] != '\0'; ++ i) {
	n[i].type=type;
	n[i].c1 = max_romaji[i];
    }
    n[i].type=OTHER;
    n[i].c1 = '\0';
    return (match_more == 0) ? max_match : -max_match;
}

int
k2a(c, n)
     Character *c;
     Character *n;
{
    return k2rom(c, n, ASCII);
}

int
k2j(c, n)
     Character *c;
     Character *n;
{
    return k2rom(c, n, JISROMAN);
}

int
k2K(c, n)
     Character *c;
     Character *n;
{
    int c1;
    static unsigned char k2K_table[64][3] = {
	"\241\241", "\241\243", "\241\326", "\241\327", "\241\242", "\241\245", "\245\362", "\245\241",
	"\245\243", "\245\245", "\245\247", "\245\251", "\245\343", "\245\345", "\245\347", "\245\303",
	"\241\274", "\245\242", "\245\244", "\245\246", "\245\250", "\245\252", "\245\253", "\245\255",
	"\245\257", "\245\261", "\245\263", "\245\265", "\245\267", "\245\271", "\245\273", "\245\275",
	"\245\277", "\245\301", "\245\304", "\245\306", "\245\310", "\245\312", "\245\313", "\245\314",
	"\245\315", "\245\316", "\245\317", "\245\322", "\245\325", "\245\330", "\245\333", "\245\336",
	"\245\337", "\245\340", "\245\341", "\245\342", "\245\344", "\245\346", "\245\350", "\245\351",
	"\245\352", "\245\353", "\245\354", "\245\355", "\245\357", "\245\363", "\241\253", "\241\254" };
    static unsigned char k2K_dtable[64][3] = {
	"",   "",   "",   "",   "",   "",   "",   "",
	"",   "",   "",   "",   "",   "",   "",   "",
	"",   "",   "",   "\245\364", "",   "",   "\245\254", "\245\256",
	"\245\260", "\245\262", "\245\264", "\245\266", "\245\270", "\245\272", "\245\274", "\245\276",
	"\245\300", "\245\302", "\245\305", "\245\307", "\245\311", "",   "",   "",
	"",   "",   "\245\320", "\245\323", "\245\326", "\245\331", "\245\334", "",
	"",   "",   "",   "",   "",   "",   "",   "",
	"",   "",   "",   "",   "",   "",   "",   "" };
    static unsigned char k2K_htable[64][3] = {
	"",   "",   "",   "",   "",   "",   "",   "",
	"",   "",   "",   "",   "",   "",   "",   "",
	"",   "",   "",   "",   "",   "",   "",   "",
	"",   "",   "",   "",   "",   "",   ""  , "",
	"",   "",   "",   "",   "",   "",   "",   "",
	"",   "",   "\245\321", "\245\324", "\245\327", "\245\332", "\245\335", "",
	"",   "",   "",   "",   "",   "",   "",   "",
	"",   "",   "",   "",   "",   "",   "",   "" };

    c1 = c -> c1;
    if (c1 > 0x60) c1 = 0x20;
    if (c[1].type==KATAKANA) {
	if ((c[1].c1==0x5e) && (k2K_dtable[c1-0x20][0] != '\0')) {
	    n[0].type=JIS83;
	    n[0].c1 = k2K_dtable[c1-0x20][0];
	    n[0].c2 = k2K_dtable[c1-0x20][1];
	    n[1].type=OTHER; n[1].c1 = '\0';
	    return 2;
	} else if ((c[1].c1==0x5f) && (k2K_htable[c1-0x20][0] != '\0')) {
	    n[0].type=JIS83;
	    n[0].c1 = k2K_htable[c1-0x20][0];
	    n[0].c2 = k2K_htable[c1-0x20][1];
	    n[1].type=OTHER; n[1].c1 = '\0';
	    return 2;
	} else {
	    n[0].type=JIS83;
	    n[0].c1 = k2K_table[c1-0x20][0];
	    n[0].c2 = k2K_table[c1-0x20][1];
	    n[1].type=OTHER; n[1].c1 = '\0';
	    return 1;
	}
    } else {
	n[0].type=JIS83;
	n[0].c1 = k2K_table[c1-0x20][0];
	n[0].c2 = k2K_table[c1-0x20][1];
	n[1].type=OTHER; n[1].c1 = '\0';
	if (k2K_dtable[c1-0x20][0] == '\0')
	    return 1;
	else
	    return -1;
    }
}
    
int
k2H(c, n)
     Character *c;
     Character *n;
{
    int c1;
    static unsigned char k2H_table[64][3] = {
	"\241\241", "\241\243", "\241\326", "\241\327", "\241\242", "\241\245", "\244\362", "\244\241",
	"\244\243", "\244\245", "\244\247", "\244\251", "\244\343", "\244\345", "\244\347", "\244\303",
	"\241\274", "\244\242", "\244\244", "\244\246", "\244\250", "\244\252", "\244\253", "\244\255",
	"\244\257", "\244\261", "\244\263", "\244\265", "\244\267", "\244\271", "\244\273", "\244\275",
	"\244\277", "\244\301", "\244\304", "\244\306", "\244\310", "\244\312", "\244\313", "\244\314",
	"\244\315", "\244\316", "\244\317", "\244\322", "\244\325", "\244\330", "\244\333", "\244\336",
	"\244\337", "\244\340", "\244\341", "\244\342", "\244\344", "\244\346", "\244\350", "\244\351",
	"\244\352", "\244\353", "\244\354", "\244\355", "\244\357", "\244\363", "\241\253", "\241\254" };
    static unsigned char k2H_dtable[64][3] = {
	"",   "",   "",   "",   "",   "",   "",   "",
	"",   "",   "",   "",   "",   "",   "",   "",
	"",   "",   "",   "",   "",   "",   "\244\254", "\244\256",
	"\244\260", "\244\262", "\244\264", "\244\266", "\244\270", "\244\272", "\244\274", "\244\276",
	"\244\300", "\244\302", "\244\305", "\244\307", "\244\311", "",   "",   "",
	"",   "",   "\244\320", "\244\323", "\244\326", "\244\331", "\244\334", "",
	"",   "",   "",   "",   "",   "",   "",   "",
	"",   "",   "",   "",   "",   "",   "",   "" };
    static unsigned char k2H_htable[64][3] = {
	"",   "",   "",   "",   "",   "",   "",   "",
	"",   "",   "",   "",   "",   "",   "",   "",
	"",   "",   "",   "",   "",   "",   "",   "",
	"",   "",   "",   "",   "",   "",   ""  , "",
	"",   "",   "",   "",   "",   "",   "",   "",
	"",   "",   "\244\321", "\244\324", "\244\327", "\244\332", "\244\335", "",
	"",   "",   "",   "",   "",   "",   "",   "",
	"",   "",   "",   "",   "",   "",   "",   "" };

    c1 = c -> c1;
    if (c1 > 0x60) c1 = 0x20;
    if (c[1].type==KATAKANA) {
	if ((c[1].c1==0x5e) && (k2H_dtable[c1-0x20][0] != '\0')) {
	    n[0].type=JIS83;
	    n[0].c1 = k2H_dtable[c1-0x20][0];
	    n[0].c2 = k2H_dtable[c1-0x20][1];
	    n[1].type=OTHER; n[1].c1 = '\0';
	    return 2;
	} else if ((c[1].c1==0x5f) && (k2H_htable[c1-0x20][0] != '\0')) {
	    n[0].type=JIS83;
	    n[0].c1 = k2H_htable[c1-0x20][0];
	    n[0].c2 = k2H_htable[c1-0x20][1];
	    n[1].type=OTHER; n[1].c1 = '\0';
	    return 2;
	} else {
	    n[0].type=JIS83;
	    n[0].c1 = k2H_table[c1-0x20][0];
	    n[0].c2 = k2H_table[c1-0x20][1];
	    n[1].type=OTHER; n[1].c1 = '\0';
	    return 1;
	}
    } else {
	n[0].type=JIS83;
	n[0].c1 = k2H_table[c1-0x20][0];
	n[0].c2 = k2H_table[c1-0x20][1];
	n[1].type=OTHER; n[1].c1 = '\0';
	if (k2H_dtable[c1-0x20][0] == '\0')
	    return 1;
	else
	    return -1;
    }
}
