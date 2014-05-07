Code and Files
==============

V této složce jsou zveřejněny všechny potřebné soubory pro správnou funkčnost celé aplikace.

ratings.txt
Toto je testovací podmnožina databáze s hodnocením od uživatelů. Data jsou uložena ve formátu user\_id | artist\_id | rank. user\_id reprezentuje 
identifikátor uživatele, který hodnotí, artist\_id reprezentuje identifikátor umělce, kterého daný uživatel hodnotí a rank pak reprezentuje konkrétní ohodnocení daného umělce uživatelem.

artists.txt
Toto je databáze, která obsahuje konkrétní názvy umělců. Data jsou uložena ve formátu artist\_id | artist\_name. artist\_id je identifikátor daného umělce a artist\_name je pak jeho konkrétní jméno.

ratings\_average.txt

ratings\_blocks.txt

ratings\_blocks\_bytes.txt

ratings\_numOfUsers.txt

user-lines.sh
Toto je jeden z podpůrných bash skriptů sloužících k předzpracování dat v databázi. Za pomoci tohoto 
