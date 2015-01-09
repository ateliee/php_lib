# 過去バージョンとの違い
## class_mail
* 送信元の文字コードが全てUTF-8にて送信するように変更しました。
* プログラム上は文字コードの変更する必要がなくなります。
* メールテンプレートもUTF-8に統一致します。

## class_template
* <?include?>関数がなくなり、<?import?>に変更されました。
* setIncludeFile()を呼び出す必要はありません。(警告が出ます)

## class_sql
* なくなりました。
* 全てclass_databaseに変更されております。
* class_sqlの関数は全て「SQL」を末尾につけてclass_databaseのメソッドにて呼び出します。(例:$c_sql->escape() → $c_database->escapeSQL())

## class_define
* 追加されました。
* これまでグローバル変数となっていた$G_SYS_****変数を全てclass_define::****に変更しています。