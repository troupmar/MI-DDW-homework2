Code and Files
==============

V této složce jsou zveřejněny všechny potřebné soubory pro správnou funkčnost celé aplikace.

<b>ratings.txt</b><br />
Toto je testovací podmnožina databáze s hodnocením od uživatelů. Data jsou uložena ve formátu <i>user\_id</i> | <i>artist\_id</i> | <i>rank</i>. <i>user\_id</i> reprezentuje 
identifikátor uživatele který hodnotí, <i>artist\_id</i> reprezentuje identifikátor umělce, kterého daný uživatel hodnotí a <i>rank</i> pak reprezentuje konkrétní ohodnocení daného umělce uživatelem.

<b>artists.txt</b><br />
Toto je databáze, která obsahuje konkrétní názvy umělců. Data jsou uložena ve formátu <i>artist\_id</i> | <i>artist\_name</i>. <i>artist\_id</i> je identifikátor daného umělce a <i>artist\_name</i> je pak jeho konkrétní jméno.

<b>ratings\_average.txt</b><br />
Tento soubor je automaticky generován aplikací pokud neexistuje a slouží k zvýšení efektivity. Obsahuje předpočítané průměrné hodnocení pro každého uživatele přes všechny jeho hodnocení. Konkrétní řádek reprezentuje konkrétního uživatele s tím, že se indexuje od 0, čili 0-tý řádek reprezentuje 0-tého uživatele.

<b>ratings\_blocks.txt</b><br />
Tento soubor je automaticky generován aplikací pokud neexistuje a slouží k zvýšení efektivity. Obsahuje předpočítané pozice jednotlivých uživatelů v hlavním souboru s hodnocením - <i>ratings.txt</i>. Soubor je ve formátu <i>start\_line</i> | <i>number\_of\_lines</i>. Na x-tém řádku je x-tý uživatel s tím, že se indexuje od 0. <i>start\_line</i> je počáteční řádek, na kterém v hlavním souboru <i>ratings.txt</i> začíná hodnocení konkrétního uživatele. <i>number\_of\_lines</i> je pak počet hodnocení konkrétního uživatele, neboli počet řádků v hlavním souboru. Jeden řádek totiž odpovídá právě jednomu hodnocení.

<b>ratings\_blocks\_bytes.txt</b><br />
Tento soubor je automaticky generován aplikací pokud neexistuje a slouží k zvýšení efektivity. Je vygenerován na základě výše zmíněného souboru <i>ratings\_blocks.txt</i>. Obsahuje stejné informace, ne však v jednotkách řádek ale v jednotkách bytů. Čili ve stejném formátu <i>byte</i> | <i>number\_of\_bytes</i> je <i>byte</i> konkrétní byte, na kterém uživatel začíná a <i>number\_of\_bytes</i> počet bytů, které v hlavním souboru uživatel zabírá. Toto umožňuje podstatně rychlejší přístup k hlavním datům za pomoci funkce fseek.

<b>ratings\_numOfUsers.txt</b><br />
Tento soubor obsahuje pouze celkový počet uživatelů, kteří hodnotili v databázi - neboli souboru <i>ratings.txt</i>.

<b>user-lines.sh</b><br />
Toto je jeden z podpůrných bash skriptů sloužících k předzpracování dat v databázi. Za pomoci tohoto skriptu je vytvořen výše uvedený soubor <i>ratings\_blocks.txt</i>.

<b>average-lines.sh</b><br />
Toto je jeden z podpůrných bash skriptů sloužících k předzpracování dat v databázi. Za pomoci tohoto skriptu je vytvořen výše uvedený soubor <i>ratings\_average.txt</i>.

<b>Database.php</b><br />
Tato třída slouží k  manipulaci s databází - neboli soubory, které obsahují všechna potřebná data pro výpočet. Pomocí této třídy jsou připravovány a generovány všechny podpůrné soubory obsahující předzpracovaná data, které umožňují zefektivnit běh aplikace. 
<b>RecommendSystem.php</b><br />
Tato třída provádí vlastní výpočet kolaborativního filtrování nad připravenými daty metodami třídy <i>Database.php</i>. Podobnost jednotlivých uživatelů probíhá na základě metriky "Pearson correlation similarity". Od nejpodobnějších uživatelů dotazovaného uživatele jsou vybrány doporučení umělců na základě jejich průměrných hodnocení a četnosti hodnocení od uživatelů. Umělec je tedy doporučen v případě, že je obsažen v množině hodnocení podobného uživatele a má vysoké průměrné hodnocení, které je ustanoveno na základě velkého počtu hodnocení. 

<b>index.php</b><br />
Tento skript využívá tříd <i>Database.php</i> a <i>RecommendSystem.php</i> k realizaci RESTového API, které na základě parametrů ve tvaru URL?user\_id=<user> vrací množinu doporučených umělců uživateli s identifikátorem <i>user\_id</i>.

<b>process.php</b><br />
Tento skript na rozdíl od skriptu <i>index.php</i> umožňuje terminálové spuštění aplikace obohacené o dodatečné informace včetně množiny umělců hodnocených dotazovaným uživatelem a času běhu aplikace.
