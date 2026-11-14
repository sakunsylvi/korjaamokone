# Korjaamokoneet.fi -- Hallintapaneelin kayttoohje

Tama ohje kattaa kaiken, mita tarvitset korjaamokoneet.fi-sivuston sisallon hallintaan.

---

## Sisallys

1. [Kirjautuminen](#1-kirjautuminen)
2. [Hallintapaneelin yleiskatsaus](#2-hallintapaneelin-yleiskatsaus)
3. [Tuotteen muokkaaminen](#3-tuotteen-muokkaaminen)
4. [Uuden tuotteen luominen](#4-uuden-tuotteen-luominen)
5. [Tuotteen poistaminen](#5-tuotteen-poistaminen)
6. [Kuvien hallinta](#6-kuvien-hallinta)
7. [Kategorian muokkaaminen](#7-kategorian-muokkaaminen)
8. [Julkaiseminen](#8-julkaiseminen)
9. [Tekniset tiedot -taulukko](#9-tekniset-tiedot--taulukko)
10. [Kuvaus (Markdown)](#10-kuvaus-markdown)
11. [SEO-asetukset](#11-seo-asetukset)
12. [Saatavuus ja hinnoittelu](#12-saatavuus-ja-hinnoittelu)
13. [Vinkkeja](#13-vinkkeja)

---

## 1. Kirjautuminen

Mene selaimella osoitteeseen:

    https://korjaamokoneet.fi/admin/

Syota kirjautumistiedot:

- **Kayttajanimi:** petri_jussila
- **Salasana:** bmw318I

Paina **Kirjaudu**. Istunto pysyy voimassa 24 tuntia, minka jalkeen sinun taytyy kirjautua uudelleen.

Kirjautuneena nakyvassa ylapalkissa on kolme linkkia:

- **Tuotteet** -- palaa tuotelistaukseen (etusivu)
- **Julkaise** -- julkaisee sivuston (lisaa tasta kohdassa 8)
- **Kirjaudu ulos** -- lopettaa istunnon

### Sivuston ja hallintapaneelin valinen vaihtopalkki

Kun olet kirjautuneena, sivun ylaosassa nakyy kapea tumma palkki jokaisella sivulla -- seka hallintapaneelissa etta julkisella sivustolla (korjaamokoneet.fi). Palkki toimii pikavalikkona naiden kahden valilla:

- **Hallintapaneelissa** palkissa lukee "korjaamokoneet.fi" ja sita klikkaamalla paasee suoraan julkiselle sivustolle.
- **Julkisella sivustolla** palkissa lukee "Hallintapaneeli" ja sita klikkaamalla paasee takaisin hallintapaneeliin.

Palkki nakyy ainoastaan sinulle kirjautuneena kayttajana -- tavalliset sivuston kavijat eivat nae sita koskaan. Jos palkki ei nay julkisella sivustolla, kokeile kovaa paivitysta (Ctrl+Shift+R tai Cmd+Shift+R).

---

## 2. Hallintapaneelin yleiskatsaus

Kirjautumisen jalkeen naat tuotelistauksen. Tama on hallintapaneelin etusivu.

**Tuotteet on ryhmitelty kategorioittain:**

- Keventimet
- Pilarinostimet
- Rengaskoneet
- Saksinostimet

Jokaisen kategorian alla nakyy siihen kuuluvat tuotteet taulukossa. Jokaisesta tuotteesta naat:

| Sarake | Sisalto |
|--------|---------|
| **Kuva** | Tuotteen ensimmainen kuva pienoiskoossa |
| **Tuote** | Tuotteen nimi ja mallikoodi |
| **Hinta** | Tuotteen hinta tai "-" jos hintaa ei ole asetettu |
| **Tila** | Saatavuusmerkinta: vihrea "Saatavilla", punainen "Ei saatavilla" tai keltainen "Tilaustuote" |
| **Toiminnot** | "Muokkaa"-painike |

Sivun ylareunassa on kaksi painiketta:

- **Luo uusi tuote** -- avaa uuden tuotteen luontidialogi
- **Julkaise sivusto** -- julkaisee muutokset live-sivustolle

Jokaisen kategoriaotsikon vieressa on **Muokkaa kategoriaa** -linkki, jolla paaset muokkaamaan kategorian teksteja.

---

## 3. Tuotteen muokkaaminen

1. Etsi tuote listalta
2. Paina tuotteen kohdalla **Muokkaa**
3. Tee haluamasi muutokset lomakkeeseen (kentat kuvattu tarkemmin kohdissa 9-12)
4. Paina **Tallenna**

Jarjestelma palaa tuotelistaukseen. Muutokset tallentuvat jarjestelmaan, mutta eivat viela nay live-sivustolla -- muista julkaista (kohta 8).

Jos et halua tallentaa muutoksia, paina **Peruuta**.

---

## 4. Uuden tuotteen luominen

### Tyhjasta luominen

1. Paina tuotelistauksen ylareunasta **Luo uusi tuote**
2. Avautuvassa ikkunassa jata pudotusvalikko asentoon "-- Tyhjasta --"
3. Paina **Luo tuote**
4. Tayta kaikki tarvittavat kentat (ks. kohdat 9-12)
5. Paina **Tallenna**

### Kloonaamalla olemassa olevasta tuotteesta

Jos uusi tuote on samankaltainen kuin jokin jo olemassa oleva tuote, voit kayttaa kloonaamista. Tama saastaa aikaa, koska tekniset tiedot, kuvaus, hinnat ja muut asetukset kopioituvat pohjaksi.

1. Paina **Luo uusi tuote**
2. Valitse pudotusvalikosta tuote, jonka pohjalta haluat luoda uuden
3. Paina **Luo tuote**
4. Lomake avautuu esitaytettyna valitun tuotteen tiedoilla

**Huom:** Kloonaus kopioi tekniset tiedot, kuvauksen, hinnat ja muut asetukset, mutta **EI kopioi kuvia**. Kuvat sinun taytyy lisata erikseen.

5. Muuta vahintaan nama kentat:
   - **Tuotteen nimi** -- anna uuden tuotteen nimi
   - **Mallikoodi** -- anna uuden tuotteen mallikoodi
6. Muokkaa muut tiedot tarpeen mukaan
7. Lisaa kuvat
8. Paina **Tallenna**

### Pakolliset kentat uudelle tuotteelle

- **Tuotteen nimi** -- esim. "HTL-3940D Kaksipilarinostin"
- **Kategoria** -- valitse pudotusvalikosta
- **Alatunniste** -- tuotteen tyyppi, esim. "kaksipilarinostin"
- **Mallikoodi** -- tuotteen tunniste, esim. "htl-3940d" (kirjoita pienilla kirjaimilla)

Nasta kolmesta kenttasta muodostuu automaattisesti sivun osoite (URL). Esimerkiksi:

    pilarinostimet-kaksipilarinostin-htl-3940d.html

Osoitteen esikatselu nakyy mallikoodi-kentan alla.

---

## 5. Tuotteen poistaminen

1. Avaa tuote painamalla **Muokkaa**
2. Tuotelomakkeen alareunassa paina punaista **Poista tuote** -painiketta
3. Selain kysyy vahvistuksen -- hyvaksy poisto

Poistoa ei voi peruuttaa. Muista julkaista (kohta 8), jotta tuote poistuu myos live-sivustolta.

---

## 6. Kuvien hallinta

Tuotteella on kahdenlaisia kuvia:

### Tuotekuvat

Naita ovat normaalit valokuvat tuotteesta. Ne nakyvat tuotesivun kuvagalleriassa.

**Kuvien lisaaminen:**
1. Avaa tuote muokattavaksi
2. Tuotekuvat-osiossa paina **Valitse tiedostot** (tai "Choose files")
3. Valitse yksi tai useampi kuva tietokoneeltasi
4. Paina **Tallenna** -- kuvat ladataan palvelimelle

**Kuvien poistaminen:**
1. Avaa tuote muokattavaksi
2. Jokaisen kuvan paalla on X-painike
3. Paina X poistaaksesi kuvan
4. Paina **Tallenna**

### Mittakuvat (diagram-kuvat)

Mittakuvat ovat teknisia piirroksia tai mittakaavioita. Ne nakyvat tuotesivulla omassa osiossaan.

Lisaaminen ja poistaminen toimii samalla tavalla kuin tuotekuvien kanssa, mutta omassa "Mittakuvat"-osiossaan.

### Kuvatiedostoista

- Sallitut tiedostotyypit: JPG, PNG, GIF, WebP
- Maksimikoko: 5 MB per kuva
- Kuva sailyttaa oman tiedostonimensa, joten nimea kuva kuvaavasti ennen latausta (hyva hakukoneille), esim. `kaksipilarinostin-hauvrex-htl-3340-sahkolukollinen-ammattimalli-4t-nostokyky.jpg`
- Nimi siistitaan automaattisesti: pienet kirjaimet, ä/ö/å -> a/o/a, valilyonnit, alaviivat ja muut erikoismerkit muutetaan viivoiksi. Esim. `Ääkkös Testi_Kuva.JPEG` -> `aakkos-testi-kuva.jpg`
- Jos samanniminen kuva on jo palvelimella, uuteen lisataan loppuun `-2`, `-3` jne. -- vanhaa kuvaa ei koskaan korvata
- Jos nimesta ei jaa mitaan kayttokelpoista (esim. pelkkia erikoismerkkeja), kuva nimetaan tuotteen tietojen perusteella

---

## 7. Kategorian muokkaaminen

Kategorioita ei voi lisata tai poistaa hallintapaneelista, mutta niiden teksteja voi muokata.

1. Tuotelistauksessa paina kategorian nimen vieressa olevaa **Muokkaa kategoriaa** -linkkia
2. Muokkaa seuraavia kenttia:

| Kentta | Missa nakyy |
|--------|-------------|
| **Esittelyteksti** | Kategoriasivun ylaosassa, ennen tuotelistausta |
| **Etusivun kuvaus** | Etusivun kategoriakortissa |
| **Meta-kuvaus** | Hakukonetuloksissa (Google) |
| **Jarjestys** | Maarittelee kategorioiden jarjestyksen (pienempi numero = aiemmin) |

3. Paina **Tallenna**

Muista julkaista muutosten jalkeen (kohta 8).

---

## 8. Julkaiseminen

**Tama on hallintapaneelin tarkeimmat asia ymmartaa:**

Kun muokkaat tuotteita, kategorioita tai lisaat kuvia, muutokset tallentuvat hallintapaneelin tietokantaan -- mutta **eivat nay viela live-sivustolla**. Sivusto on staattinen HTML-sivusto, joka taytyy generoida uudelleen aina muutosten jalkeen.

### Miten julkaistaan

1. Paina ylapalkin **Julkaise**-linkkia tai tuotelistan **Julkaise sivusto** -painiketta
2. Jarjestelma generoi kaikki sivut uudelleen: etusivu, kategoriasivut, tuotesivut ja hakutiedoston
3. Naat julkaisun tulokset

### Julkaisun tulossivu

Julkaisun jalkeen naet yhteenvedon:

- **"Sivusto julkaistu onnistuneesti"** -- kaikki meni hyvin
- **"Julkaisussa tapahtui virheita"** -- jokin meni pieleen (virheet listataan)

Tulossivu nayttaa:

- **Luodut tiedostot** -- lista kaikista generoiduista HTML-sivuista (esim. etusivu, kategoriasivut, tuotesivut)
- **Poistetut tiedostot** -- sivut jotka poistettiin (esim. poistetun tuotteen sivu)
- **Virheet** -- jos jokin epaonnistui

Jos naet "Ei muutoksia julkaistavaksi", kaikki sivut olivat jo ajan tasalla.

### Muistisaanto

**Tee muutokset -> Tallenna -> Julkaise -> Tarkista live-sivusto.**

Julkaiseminen ei koskaan riko mitaan -- se vain generoi sivut uudelleen nykyisten tietojen perusteella. Voit julkaista niin usein kuin haluat.

---

## 9. Tekniset tiedot -taulukko

Tuotteen muokkauslomakkeessa on "Tekniset tiedot" -osio. Tassa syotetaan ominaisuus-arvo -pareja, jotka nakyvat tuotesivulla siistina taulukkona.

Esimerkki:

| Ominaisuus | Arvo |
|-----------|------|
| Nostokapasiteetti | 4000 kg |
| Nostokorkeus | 1850 mm |
| Kokonaisleveys | 3406 mm |

### Rivien lisaaminen

- Paina **Lisaa rivi** -painiketta
- Tayta vasemmalle ominaisuuden nimi (esim. "Nostokapasiteetti") ja oikealle arvo (esim. "4000 kg")

### Rivien poistaminen

- Paina rivin oikeassa reunassa olevaa X-painiketta

Tyhjat rivit (joissa joko ominaisuus tai arvo puuttuu) jatetaan automaattisesti huomiotta tallennettaessa.

**Huom:** "Ominaisuudet listaukseen" on erillinen kentta, joka nakyy kategoriasivulla tuotekorttien alla lyhyena listana. Se on pelkka tekstikentta, johon kirjoitat yhden ominaisuuden per rivi.

---

## 10. Kuvaus (Markdown)

Kuvaus-kentta kayttaa Markdown-muotoilua. Tama tarkoittaa, etta voit kirjoittaa muotoiltua tekstia yksinkertaisilla merkinnoilla.

### Perussaannot

**Kappaleet:** Jata tyhja rivi kappaleiden valiin.

```
Tama on ensimmainen kappale.

Tama on toinen kappale.
```

**Lihavointi:** Kirjoita sana kahden tahden valiin.

```
Tama sana on **lihavoitu**.
```

**Kursiivi:** Kirjoita sana yhden tahden valiin.

```
Tama sana on *kursivoitu*.
```

**Linkki:** Linkin teksti hakasulkeisiin, osoite sulkeisiin.

```
Katso lisaa [valmistajan sivuilta](https://www.esimerkki.fi).
```

Markdown-muotoilu muunnetaan automaattisesti HTML:ksi julkaisun yhteydessa, joten lopputulos nayttaa siistilta tuotesivulla.

---

## 11. SEO-asetukset

Tuotteen muokkauslomakkeen alareunassa on kaksi SEO:hon liittyvaa kenttaa:

### Meta-kuvaus

Kirjoita lyhyt (1-2 lausetta) kuvaus tuotteesta. Tama teksti saattaa nakya Googlen hakutuloksissa tuotesivun otsikon alla. Hyva meta-kuvaus houkuttelee klikkaamaan.

Esimerkki: "HTL-3940D on laadukas kaksipilarinostin 4000 kg nostokapasiteetilla. Sopii henkiloautojen ja pakettiautojen huoltoon."

### Jarjestys

Numero joka maarittelee tuotteen sijainnin kategorialistalla. Pienempi numero tarkoittaa, etta tuote nakyy listalla ylempana. Oletusarvo on 0.

Jos haluat etta tietty tuote nakyy ensimmaisena, anna sille jarjestysnumero 1. Jos haluat etta toinen tulee heti peraan, anna 2, ja niin edelleen.

---

## 12. Saatavuus ja hinnoittelu

### Saatavuus

Tuotteella on kolme mahdollista saatavuustilaa:

| Tila | Mita tarkoittaa | Miten nakyy |
|------|----------------|-------------|
| **Saatavilla** | Tuote on varastossa ja toimitettavissa | Vihrea merkinta |
| **Ei saatavilla** | Tuotetta ei ole saatavilla | Punainen merkinta |
| **Tilaustuote** | Tuote tilataan erikseen | Keltainen merkinta |

Valitse oikea tila pudotusvalikosta.

### Hinta

Kirjoita hinta tekstina juuri niin kuin haluat sen nakyvat sivustolla, esimerkiksi:

- `2 790 EUR sis. alv`
- `Hinta pyydettaessa`
- `4 500 EUR + alv`

Hinta on vapaamuotoinen tekstikentta -- voit kirjoittaa siihen mita tahansa.

### Alkuperainen hinta

Jos tuote on tarjouksessa, voit kirjoittaa alkuperaisen hinnan tahan kenttaan. Se nakyy tuotesivulla yliviivattuna hinnan vieressa, jolloin asiakas nakyy alennuksen.

Esimerkiksi:
- Hinta: `2 490 EUR sis. alv`
- Alkuperainen hinta: `2 790 EUR sis. alv`

Jata tyhjaksi jos tuote ei ole tarjouksessa.

### Hintahuomio

Lisatietokentta hintaan liittyen. Nakyy tuotesivulla hinnan yhteydessa. Esimerkiksi "Sisaltaa asennuksen" tai "Rahti sovitaan erikseen".

---

## 13. Vinkkeja

- **Julkaise aina muutosten jalkeen.** Mika tahansa muutos -- tuotteen muokkaus, uusi tuote, kuvan lisays, kategoriatekstin paivitys -- vaatii julkaisun ennen kuin se nakyy sivustolla.

- **Kayta kloonausta samanlaisten tuotteiden luomisessa.** Jos lisaat tuotteen joka on samankaltainen kuin jo olemassa oleva, kloonaaminen saastaa paljon aikaa.

- **Tarkista live-sivusto julkaisun jalkeen.** Avaa korjaamokoneet.fi selaimessa ja varmista, etta muutokset nakyvat oikein. Jos sivu nayttaa vanhan version, kokeile paivittaa sivu (Ctrl+F5 tai Cmd+Shift+R).

- **Mallikoodi pienilla kirjaimilla.** Kirjoita mallikoodi aina pienilla kirjaimilla (esim. "htl-3940d", ei "HTL-3940D"). Jarjestelma muuntaa sen automaattisesti, mutta on selvempaa kirjoittaa se oikein alusta asti.

- **Kuvien jarjestys.** Ensimmainen tuotekuva nakyy pienennoiskuvana tuotelistauksessa. Lisaa siis paras kuva ensimmaisena.

- **Voit julkaista milloin tahansa.** Julkaiseminen ei koskaan riko sivustoa. Se generoi kaikki sivut uudelleen tietokannan nykyisilla tiedoilla.

- **Alatunniste pysyy samana saman kategorian tuotteilla.** Esimerkiksi kaikki kaksipilarinostimet kayttavat alatunnistetta "kaksipilarinostin". Katso olemassa olevista tuotteista mallia.
