<?php
$pageTitle = 'Käyttöohje';

ob_start();
?>

<div class="page-header">
    <h1>Käyttöohje</h1>
</div>

<div class="card guide-content">

<p>Tämä ohje kattaa kaiken, mitä tarvitset korjaamokoneet.fi-sivuston sisällön hallintaan.</p>

<hr>

<h2>Sisällys</h2>

<ol>
    <li><a href="#kirjautuminen">Kirjautuminen</a></li>
    <li><a href="#yleiskatsaus">Hallintapaneelin yleiskatsaus</a></li>
    <li><a href="#muokkaaminen">Tuotteen muokkaaminen</a></li>
    <li><a href="#luominen">Uuden tuotteen luominen</a></li>
    <li><a href="#poistaminen">Tuotteen poistaminen</a></li>
    <li><a href="#kuvat">Kuvien hallinta</a></li>
    <li><a href="#kategoria">Kategorian muokkaaminen</a></li>
    <li><a href="#julkaiseminen">Julkaiseminen</a></li>
    <li><a href="#tekniset-tiedot">Tekniset tiedot -taulukko</a></li>
    <li><a href="#kuvaus">Kuvaus (Markdown)</a></li>
    <li><a href="#seo">SEO-asetukset</a></li>
    <li><a href="#saatavuus">Saatavuus ja hinnoittelu</a></li>
    <li><a href="#vinkit">Vinkkejä</a></li>
</ol>

<hr>

<h2 id="kirjautuminen">1. Kirjautuminen</h2>

<p>Mene selaimella osoitteeseen:</p>

<pre><code>https://korjaamokoneet.fi/admin/</code></pre>

<p>Syötä kirjautumistiedot ja paina <strong>Kirjaudu</strong>. Istunto pysyy voimassa 24 tuntia, minkä jälkeen sinun täytyy kirjautua uudelleen.</p>

<p>Kirjautuneena näkyvässä yläpalkissa on kolme linkkiä:</p>

<ul>
    <li><strong>Tuotteet</strong> &ndash; palaa tuotelistaukseen (etusivu)</li>
    <li><strong>Julkaise</strong> &ndash; julkaisee sivuston (lisää tästä kohdassa 8)</li>
    <li><strong>Kirjaudu ulos</strong> &ndash; lopettaa istunnon</li>
</ul>

<h3>Sivuston ja hallintapaneelin välinen vaihtopalkki</h3>

<p>Kun olet kirjautuneena, sivun yläosassa näkyy kapea tumma palkki jokaisella sivulla &ndash; sekä hallintapaneelissa että julkisella sivustolla (korjaamokoneet.fi). Palkki toimii pikavalikona näiden kahden välillä:</p>

<ul>
    <li><strong>Hallintapaneelissa</strong> palkissa lukee &quot;korjaamokoneet.fi&quot; ja sitä klikkaamalla pääsee suoraan julkiselle sivustolle.</li>
    <li><strong>Julkisella sivustolla</strong> palkissa lukee &quot;Hallintapaneeli&quot; ja sitä klikkaamalla pääsee takaisin hallintapaneeliin.</li>
</ul>

<p>Palkki näkyy ainoastaan sinulle kirjautuneena käyttäjänä &ndash; tavalliset sivuston kävijät eivät näe sitä koskaan. Jos palkki ei näy julkisella sivustolla, kokeile kovaa päivitystä (Ctrl+Shift+R tai Cmd+Shift+R).</p>

<hr>

<h2 id="yleiskatsaus">2. Hallintapaneelin yleiskatsaus</h2>

<p>Kirjautumisen jälkeen näät tuotelistauksen. Tämä on hallintapaneelin etusivu.</p>

<p><strong>Tuotteet on ryhmitelty kategorioittain:</strong></p>

<ul>
    <li>Keventimet</li>
    <li>Pilarinostimet</li>
    <li>Rengaskoneet</li>
    <li>Saksinostimet</li>
</ul>

<p>Jokaisen kategorian alla näkyy siihen kuuluvat tuotteet taulukossa. Jokaisesta tuotteesta näät:</p>

<table>
    <thead>
        <tr>
            <th>Sarake</th>
            <th>Sisältö</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Kuva</strong></td>
            <td>Tuotteen ensimmäinen kuva pienoiskoossa</td>
        </tr>
        <tr>
            <td><strong>Tuote</strong></td>
            <td>Tuotteen nimi ja mallikoodi</td>
        </tr>
        <tr>
            <td><strong>Hinta</strong></td>
            <td>Tuotteen hinta tai &quot;-&quot; jos hintaa ei ole asetettu</td>
        </tr>
        <tr>
            <td><strong>Tila</strong></td>
            <td>Saatavuusmerkintä: vihreä &quot;Saatavilla&quot;, punainen &quot;Ei saatavilla&quot; tai keltainen &quot;Tilaustuote&quot;</td>
        </tr>
        <tr>
            <td><strong>Toiminnot</strong></td>
            <td>&quot;Muokkaa&quot;-painike</td>
        </tr>
    </tbody>
</table>

<p>Sivun yläreunassa on kaksi painiketta:</p>

<ul>
    <li><strong>Luo uusi tuote</strong> &ndash; avaa uuden tuotteen luontidialogi</li>
    <li><strong>Julkaise sivusto</strong> &ndash; julkaisee muutokset live-sivustolle</li>
</ul>

<p>Jokaisen kategoriaotsikon vieressä on <strong>Muokkaa kategoriaa</strong> -linkki, jolla pääset muokkaamaan kategorian tekstejä.</p>

<hr>

<h2 id="muokkaaminen">3. Tuotteen muokkaaminen</h2>

<ol>
    <li>Etsi tuote listalta</li>
    <li>Paina tuotteen kohdalla <strong>Muokkaa</strong></li>
    <li>Tee haluamasi muutokset lomakkeeseen (kentät kuvattu tarkemmin kohdissa 9&ndash;12)</li>
    <li>Paina <strong>Tallenna</strong></li>
</ol>

<p>Järjestelmä palaa tuotelistaukseen. Muutokset tallentuvat järjestelmään, mutta eivät vielä näy live-sivustolla &ndash; muista julkaista (kohta 8).</p>

<p>Jos et halua tallentaa muutoksia, paina <strong>Peruuta</strong>.</p>

<hr>

<h2 id="luominen">4. Uuden tuotteen luominen</h2>

<h3>Tyhjästä luominen</h3>

<ol>
    <li>Paina tuotelistauksen yläreunasta <strong>Luo uusi tuote</strong></li>
    <li>Avautuvassa ikkunassa jätä pudotusvalikko asentoon &quot;-- Tyhjästä --&quot;</li>
    <li>Paina <strong>Luo tuote</strong></li>
    <li>Täytä kaikki tarvittavat kentät (ks. kohdat 9&ndash;12)</li>
    <li>Paina <strong>Tallenna</strong></li>
</ol>

<h3>Kloonaamalla olemassa olevasta tuotteesta</h3>

<p>Jos uusi tuote on samankaltainen kuin jokin jo olemassa oleva tuote, voit käyttää kloonaamista. Tämä säästää aikaa, koska tekniset tiedot, kuvaus, hinnat ja muut asetukset kopioituvat pohjaksi.</p>

<ol>
    <li>Paina <strong>Luo uusi tuote</strong></li>
    <li>Valitse pudotusvalikosta tuote, jonka pohjalta haluat luoda uuden</li>
    <li>Paina <strong>Luo tuote</strong></li>
    <li>Lomake avautuu esitäytettynä valitun tuotteen tiedoilla</li>
</ol>

<p><strong>Huom:</strong> Kloonaus kopioi tekniset tiedot, kuvauksen, hinnat ja muut asetukset, mutta <strong>EI kopioi kuvia</strong>. Kuvat sinun täytyy lisätä erikseen.</p>

<ol start="5">
    <li>Muuta vähintään nämä kentät:
        <ul>
            <li><strong>Tuotteen nimi</strong> &ndash; anna uuden tuotteen nimi</li>
            <li><strong>Mallikoodi</strong> &ndash; anna uuden tuotteen mallikoodi</li>
        </ul>
    </li>
    <li>Muokkaa muut tiedot tarpeen mukaan</li>
    <li>Lisää kuvat</li>
    <li>Paina <strong>Tallenna</strong></li>
</ol>

<h3>Pakolliset kentät uudelle tuotteelle</h3>

<ul>
    <li><strong>Tuotteen nimi</strong> &ndash; esim. &quot;HTL-3940D Kaksipilarinostin&quot;</li>
    <li><strong>Kategoria</strong> &ndash; valitse pudotusvalikosta</li>
    <li><strong>Alatunniste</strong> &ndash; tuotteen tyyppi, esim. &quot;kaksipilarinostin&quot;</li>
    <li><strong>Mallikoodi</strong> &ndash; tuotteen tunniste, esim. &quot;htl-3940d&quot; (kirjoita pienillä kirjaimilla)</li>
</ul>

<p>Näistä kolmesta kentästä muodostuu automaattisesti sivun osoite (URL). Esimerkiksi:</p>

<pre><code>pilarinostimet-kaksipilarinostin-htl-3940d.html</code></pre>

<p>Osoitteen esikatselu näkyy mallikoodi-kentän alla.</p>

<hr>

<h2 id="poistaminen">5. Tuotteen poistaminen</h2>

<ol>
    <li>Avaa tuote painamalla <strong>Muokkaa</strong></li>
    <li>Tuotelomakkeen alareunassa paina punaista <strong>Poista tuote</strong> -painiketta</li>
    <li>Selain kysyy vahvistuksen &ndash; hyväksy poisto</li>
</ol>

<p>Poistoa ei voi peruuttaa. Muista julkaista (kohta 8), jotta tuote poistuu myös live-sivustolta.</p>

<hr>

<h2 id="kuvat">6. Kuvien hallinta</h2>

<p>Tuotteella on kahdenlaisia kuvia:</p>

<h3>Tuotekuvat</h3>

<p>Näitä ovat normaalit valokuvat tuotteesta. Ne näkyvät tuotesivun kuvagalleriassa.</p>

<p><strong>Kuvien lisääminen:</strong></p>
<ol>
    <li>Avaa tuote muokattavaksi</li>
    <li>Tuotekuvat-osiossa paina <strong>Valitse tiedostot</strong> (tai &quot;Choose files&quot;)</li>
    <li>Valitse yksi tai useampi kuva tietokoneeltasi</li>
    <li>Paina <strong>Tallenna</strong> &ndash; kuvat ladataan palvelimelle</li>
</ol>

<p><strong>Kuvien poistaminen:</strong></p>
<ol>
    <li>Avaa tuote muokattavaksi</li>
    <li>Jokaisen kuvan päällä on X-painike</li>
    <li>Paina X poistaaksesi kuvan</li>
    <li>Paina <strong>Tallenna</strong></li>
</ol>

<h3>Mittakuvat (diagram-kuvat)</h3>

<p>Mittakuvat ovat teknisiä piirroksia tai mittakaavioita. Ne näkyvät tuotesivulla omassa osiossaan.</p>

<p>Lisääminen ja poistaminen toimii samalla tavalla kuin tuotekuvien kanssa, mutta omassa &quot;Mittakuvat&quot;-osiossaan.</p>

<h3>Kuvatiedostoista</h3>

<ul>
    <li>Sallitut tiedostotyypit: JPG, PNG, GIF, WebP</li>
    <li>Maksimikoko: 5 MB per kuva</li>
    <li>Kuva säilyttää oman tiedostonimensä, joten nimeä kuva kuvaavasti ennen latausta (hyvä hakukoneille), esim. <code>kaksipilarinostin-hauvrex-htl-3340-sahkolukollinen-ammattimalli-4t-nostokyky.jpg</code></li>
    <li>Nimi siistitään automaattisesti: pienet kirjaimet, ä/ö/å &rarr; a/o/a, välilyönnit, alaviivat ja muut erikoismerkit muutetaan viivoiksi. Esim. <code>Ääkkös Testi_Kuva.JPEG</code> &rarr; <code>aakkos-testi-kuva.jpg</code></li>
    <li>Jos samanniminen kuva on jo palvelimella, uuteen lisätään loppuun <code>-2</code>, <code>-3</code> jne. &ndash; vanhaa kuvaa ei koskaan korvata</li>
    <li>Jos nimestä ei jää mitään käyttökelpoista (esim. pelkkiä erikoismerkkejä), kuva nimetään tuotteen tietojen perusteella</li>
</ul>

<hr>

<h2 id="kategoria">7. Kategorian muokkaaminen</h2>

<p>Kategorioita ei voi lisätä tai poistaa hallintapaneelista, mutta niiden tekstejä voi muokata.</p>

<ol>
    <li>Tuotelistauksessa paina kategorian nimen vieressä olevaa <strong>Muokkaa kategoriaa</strong> -linkkiä</li>
    <li>Muokkaa seuraavia kenttiä:</li>
</ol>

<table>
    <thead>
        <tr>
            <th>Kenttä</th>
            <th>Missä näkyy</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Esittelyteksti</strong></td>
            <td>Kategoriasivun yläosassa, ennen tuotelistausta</td>
        </tr>
        <tr>
            <td><strong>Etusivun kuvaus</strong></td>
            <td>Etusivun kategoriakortissa</td>
        </tr>
        <tr>
            <td><strong>Meta-kuvaus</strong></td>
            <td>Hakukonetuloksissa (Google)</td>
        </tr>
        <tr>
            <td><strong>Järjestys</strong></td>
            <td>Määrittelee kategorioiden järjestyksen (pienempi numero = aiemmin)</td>
        </tr>
    </tbody>
</table>

<ol start="3">
    <li>Paina <strong>Tallenna</strong></li>
</ol>

<p>Muista julkaista muutosten jälkeen (kohta 8).</p>

<hr>

<h2 id="julkaiseminen">8. Julkaiseminen</h2>

<p><strong>Tämä on hallintapaneelin tärkein asia ymmärtää:</strong></p>

<p>Kun muokkaat tuotteita, kategorioita tai lisäät kuvia, muutokset tallentuvat hallintapaneelin tietokantaan &ndash; mutta <strong>eivät näy vielä live-sivustolla</strong>. Sivusto on staattinen HTML-sivusto, joka täytyy generoida uudelleen aina muutosten jälkeen.</p>

<h3>Miten julkaistaan</h3>

<ol>
    <li>Paina yläpalkin <strong>Julkaise</strong>-linkkiä tai tuotelistan <strong>Julkaise sivusto</strong> -painiketta</li>
    <li>Järjestelmä generoi kaikki sivut uudelleen: etusivu, kategoriasivut, tuotesivut ja hakutiedoston</li>
    <li>Näät julkaisun tulokset</li>
</ol>

<h3>Julkaisun tulossivu</h3>

<p>Julkaisun jälkeen näet yhteenvedon:</p>

<ul>
    <li><strong>&quot;Sivusto julkaistu onnistuneesti&quot;</strong> &ndash; kaikki meni hyvin</li>
    <li><strong>&quot;Julkaisussa tapahtui virheitä&quot;</strong> &ndash; jokin meni pieleen (virheet listataan)</li>
</ul>

<p>Tulossivu näyttää:</p>

<ul>
    <li><strong>Luodut tiedostot</strong> &ndash; lista kaikista generoiduista HTML-sivuista (esim. etusivu, kategoriasivut, tuotesivut)</li>
    <li><strong>Poistetut tiedostot</strong> &ndash; sivut jotka poistettiin (esim. poistetun tuotteen sivu)</li>
    <li><strong>Virheet</strong> &ndash; jos jokin epäonnistui</li>
</ul>

<p>Jos näet &quot;Ei muutoksia julkaistavaksi&quot;, kaikki sivut olivat jo ajan tasalla.</p>

<h3>Muistisääntö</h3>

<p><strong>Tee muutokset &rarr; Tallenna &rarr; Julkaise &rarr; Tarkista live-sivusto.</strong></p>

<p>Julkaiseminen ei koskaan riko mitään &ndash; se vain generoi sivut uudelleen nykyisten tietojen perusteella. Voit julkaista niin usein kuin haluat.</p>

<hr>

<h2 id="tekniset-tiedot">9. Tekniset tiedot -taulukko</h2>

<p>Tuotteen muokkauslomakkeessa on &quot;Tekniset tiedot&quot; -osio. Tässä syötetään ominaisuus-arvo -pareja, jotka näkyvät tuotesivulla siistinä taulukkona.</p>

<p>Esimerkki:</p>

<table>
    <thead>
        <tr>
            <th>Ominaisuus</th>
            <th>Arvo</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Nostokapasiteetti</td>
            <td>4000 kg</td>
        </tr>
        <tr>
            <td>Nostokorkeus</td>
            <td>1850 mm</td>
        </tr>
        <tr>
            <td>Kokonaisleveys</td>
            <td>3406 mm</td>
        </tr>
    </tbody>
</table>

<h3>Rivien lisääminen</h3>

<ul>
    <li>Paina <strong>Lisää rivi</strong> -painiketta</li>
    <li>Täytä vasemmalle ominaisuuden nimi (esim. &quot;Nostokapasiteetti&quot;) ja oikealle arvo (esim. &quot;4000 kg&quot;)</li>
</ul>

<h3>Rivien poistaminen</h3>

<ul>
    <li>Paina rivin oikeassa reunassa olevaa X-painiketta</li>
</ul>

<p>Tyhjät rivit (joissa joko ominaisuus tai arvo puuttuu) jätetään automaattisesti huomiotta tallennettaessa.</p>

<p><strong>Huom:</strong> &quot;Ominaisuudet listaukseen&quot; on erillinen kenttä, joka näkyy kategoriasivulla tuotekorttien alla lyhyenä listana. Se on pelkkä tekstikenttä, johon kirjoitat yhden ominaisuuden per rivi.</p>

<hr>

<h2 id="kuvaus">10. Kuvaus (Markdown)</h2>

<p>Kuvaus-kenttä käyttää Markdown-muotoilua. Tämä tarkoittaa, että voit kirjoittaa muotoiltua tekstiä yksinkertaisilla merkinnöillä.</p>

<h3>Perussäännöt</h3>

<p><strong>Kappaleet:</strong> Jätä tyhjä rivi kappaleiden väliin.</p>

<pre><code>Tämä on ensimmäinen kappale.

Tämä on toinen kappale.</code></pre>

<p><strong>Lihavointi:</strong> Kirjoita sana kahden tähden väliin.</p>

<pre><code>Tämä sana on **lihavoitu**.</code></pre>

<p><strong>Kursiivi:</strong> Kirjoita sana yhden tähden väliin.</p>

<pre><code>Tämä sana on *kursivoitu*.</code></pre>

<p><strong>Linkki:</strong> Linkin teksti hakasulkeisiin, osoite sulkeisiin.</p>

<pre><code>Katso lisää [valmistajan sivuilta](https://www.esimerkki.fi).</code></pre>

<p>Markdown-muotoilu muunnetaan automaattisesti HTML:ksi julkaisun yhteydessä, joten lopputulos näyttää siistiltä tuotesivulla.</p>

<hr>

<h2 id="seo">11. SEO-asetukset</h2>

<p>Tuotteen muokkauslomakkeen alareunassa on kaksi SEO:hon liittyvää kenttää:</p>

<h3>Meta-kuvaus</h3>

<p>Kirjoita lyhyt (1&ndash;2 lausetta) kuvaus tuotteesta. Tämä teksti saattaa näkyä Googlen hakutuloksissa tuotesivun otsikon alla. Hyvä meta-kuvaus houkuttelee klikkaamaan.</p>

<p>Esimerkki: &quot;HTL-3940D on laadukas kaksipilarinostin 4000 kg nostokapasiteetilla. Sopii henkilöautojen ja pakettiautojen huoltoon.&quot;</p>

<h3>Järjestys</h3>

<p>Numero joka määrittelee tuotteen sijainnin kategorialistalla. Pienempi numero tarkoittaa, että tuote näkyy listalla ylempänä. Oletusarvo on 0.</p>

<p>Jos haluat että tietty tuote näkyy ensimmäisenä, anna sille järjestysnumero 1. Jos haluat että toinen tulee heti perään, anna 2, ja niin edelleen.</p>

<hr>

<h2 id="saatavuus">12. Saatavuus ja hinnoittelu</h2>

<h3>Saatavuus</h3>

<p>Tuotteella on kolme mahdollista saatavuustilaa:</p>

<table>
    <thead>
        <tr>
            <th>Tila</th>
            <th>Mitä tarkoittaa</th>
            <th>Miten näkyy</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Saatavilla</strong></td>
            <td>Tuote on varastossa ja toimitettavissa</td>
            <td>Vihreä merkintä</td>
        </tr>
        <tr>
            <td><strong>Ei saatavilla</strong></td>
            <td>Tuotetta ei ole saatavilla</td>
            <td>Punainen merkintä</td>
        </tr>
        <tr>
            <td><strong>Tilaustuote</strong></td>
            <td>Tuote tilataan erikseen</td>
            <td>Keltainen merkintä</td>
        </tr>
    </tbody>
</table>

<p>Valitse oikea tila pudotusvalikosta.</p>

<h3>Hinta</h3>

<p>Kirjoita hinta tekstinä juuri niin kuin haluat sen näkyvän sivustolla, esimerkiksi:</p>

<ul>
    <li><code>2 790 EUR sis. alv</code></li>
    <li><code>Hinta pyydettäessä</code></li>
    <li><code>4 500 EUR + alv</code></li>
</ul>

<p>Hinta on vapaamuotoinen tekstikenttä &ndash; voit kirjoittaa siihen mitä tahansa.</p>

<h3>Alkuperäinen hinta</h3>

<p>Jos tuote on tarjouksessa, voit kirjoittaa alkuperäisen hinnan tähän kenttään. Se näkyy tuotesivulla yliviivattuna hinnan vieressä, jolloin asiakas näkee alennuksen.</p>

<p>Esimerkiksi:</p>
<ul>
    <li>Hinta: <code>2 490 EUR sis. alv</code></li>
    <li>Alkuperäinen hinta: <code>2 790 EUR sis. alv</code></li>
</ul>

<p>Jätä tyhjäksi jos tuote ei ole tarjouksessa.</p>

<h3>Hintahuomio</h3>

<p>Lisätietokenttä hintaan liittyen. Näkyy tuotesivulla hinnan yhteydessä. Esimerkiksi &quot;Sisältää asennuksen&quot; tai &quot;Rahti sovitaan erikseen&quot;.</p>

<hr>

<h2 id="vinkit">13. Vinkkejä</h2>

<ul>
    <li><strong>Julkaise aina muutosten jälkeen.</strong> Mikä tahansa muutos &ndash; tuotteen muokkaus, uusi tuote, kuvan lisäys, kategoriatekstin päivitys &ndash; vaatii julkaisun ennen kuin se näkyy sivustolla.</li>
    <li><strong>Käytä kloonausta samanlaisten tuotteiden luomisessa.</strong> Jos lisäät tuotteen joka on samankaltainen kuin jo olemassa oleva, kloonaaminen säästää paljon aikaa.</li>
    <li><strong>Tarkista live-sivusto julkaisun jälkeen.</strong> Avaa korjaamokoneet.fi selaimessa ja varmista, että muutokset näkyvät oikein. Jos sivu näyttää vanhan version, kokeile päivittää sivu (Ctrl+F5 tai Cmd+Shift+R).</li>
    <li><strong>Mallikoodi pienillä kirjaimilla.</strong> Kirjoita mallikoodi aina pienillä kirjaimilla (esim. &quot;htl-3940d&quot;, ei &quot;HTL-3940D&quot;). Järjestelmä muuntaa sen automaattisesti, mutta on selvempää kirjoittaa se oikein alusta asti.</li>
    <li><strong>Kuvien järjestys.</strong> Ensimmäinen tuotekuva näkyy pienoiskuvana tuotelistauksessa. Lisää siis paras kuva ensimmäisenä.</li>
    <li><strong>Voit julkaista milloin tahansa.</strong> Julkaiseminen ei koskaan riko sivustoa. Se generoi kaikki sivut uudelleen tietokannan nykyisillä tiedoilla.</li>
    <li><strong>Alatunniste pysyy samana saman kategorian tuotteilla.</strong> Esimerkiksi kaikki kaksipilarinostimet käyttävät alatunnistetta &quot;kaksipilarinostin&quot;. Katso olemassa olevista tuotteista mallia.</li>
</ul>

</div>

<div style="margin-top:20px;">
    <a href="index.php" class="btn">Takaisin</a>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
