# Prokom — MVP

## Çfarë është Prokom

**Prokom** është një platformë web multi-tenant për menaxhimin e kompanive që punojnë me projekte, ekipe dhe operacione në terren (p.sh. ndërtim, shërbime, kontraktorë).

Sistemi i jep kompanisë një vend të vetëm ku mund të:

- menaxhojë punonjësit, kontratat dhe pushimet
- ndjekë projektet, detyrat dhe raportet nga terreni
- mbajë klientët, furnizuesit, ofertat dhe materialet
- evidencojë lëvizjet financiare dhe stoqet
- komunikojë brenda kompanisë përmes lajmeve

Çdo kompani punon në hapësirën e vet (`company_id`): të dhënat e një kompanie nuk përziehen me të tjerat. Interfaqja është në **shqip**.

---

## Për kë është

| Përdorues | Çfarë bën në sistem |
|-----------|---------------------|
| **Admin** | Menaxhon kompanitë e regjistruara dhe paketat e abonimit |
| **Kompania** | Përdoruesi kryesor i biznesit — akses i plotë në modulet e kompanisë |
| **Menaxher / Finance / HR / Employee** | Role të brendshme të punonjësve me qasje sipas menysë së sistemit |

---

## Çfarë përmban sistemi

### 1. Hyrja dhe llogaria

Sistemi ofron hyrje për admin dhe kompani, regjistrim të kompanisë së re, profil përdoruesi dhe ndryshim fjalëkalimi. Pas hyrjes, përdoruesi kalon në **Dashboard** si pasqyrë e përgjithshme e aktivitetit.

Admini mund të shohë listën e kompanive, detajet e tyre, të ndryshojë statusin dhe të menaxhojë **paketat** e abonimit.

---

### 2. Punonjësit dhe burimet njerëzore

**Punonjësit** — kompania regjistron stafin me të dhëna bazë dhe role (p.sh. menaxher, punonjës, finance, HR). Mund t’i përditësojë, t’i bllokojë ose t’i aktivizojë.

**Kontratat** — menaxhim i kontratave të punës: krijim, editim, detaje dhe dokumente të bashkëngjitura.

**Pushimet** — kërkesa dhe evidenca e pushimeve, me ndryshim statusi (p.sh. në pritje / aprovuar / refuzuar).

**Orët e punës dhe check-in** — evidencë e kohës së punës: fillim pune, pushim, mbarim pune, si dhe pamje e orëve të punës.

---

### 3. Projektet dhe puna në terren

**Projektet** — krijim dhe menaxhim i projekteve me të dhëna si emër, menaxher, klient, lokacion, buxhet, data dhe status. Ka listë, detaje, editim dhe ndryshim statusi. Nga topbar-i mund të zgjidhet një projekt aktiv (konteksti i projektit) për të lehtësuar navigimin.

**Detyrat** — detyra të lidhura me projekte: titull, përshkrim, afat, status dhe caktim te një ose disa punonjës. Lista tregon kush është caktuar dhe kush e ka caktuar; ka detaje, editim dhe fshirje.

**Raportet e projekteve** — raporte për progresin e projektit, me përshkrim, status dhe mundësi ngarkimi imazhesh nga terreni. Në listë shfaqen edhe të dhëna kontekstuale (projekt, menaxher, klient, etj.).

**Raportet e detyrave** — raporte për detyra konkrete, me status, imazhe dhe informacion për projektin / punonjësit e lidhur.

---

### 4. Klientët, furnizuesit dhe ofertat

**Klientët** — regjistri i klientëve të kompanisë (CRUD).

**Furnizuesit** — regjistri i furnitorëve (CRUD).

**Ofertat** — krijim i ofertave në hapa (klient, detaje, përmbajtje), editim, faturë/printim, ndryshim statusi dhe fshirje. Ofertat lidhin klientin me shërbimet/materialet e ofruara.

---

### 5. Materialet dhe stoqet

**Materialet** — katalog i materialeve që përdor kompania.

**Stoqet** — hyrje/dalje dhe gjendje stoku për materiale.

**Raporti i stokut** — pamje e përmbledhur e gjendjes së materialeve.

---

### 6. Financa

**Transaksionet financiare** — regjistrim i lëvizjeve (të ardhura / shpenzime), editim dhe fshirje.

**Raporti financiar** — përmbledhje e transaksioneve për ndjekje të thjeshtë të situatës financiare.

---

### 7. Komunikimi i brendshëm

**Lajmet e kompanisë** — njoftime / lajme të brendshme që kompania i publikon për stafin (krijim, editim, fshirje).

---

## Si funksionon në praktikë

Një kompani tipike e përdor Prokom kështu:

1. Regjistrohet ose aktivizohet nga admini dhe hyn në sistem.
2. Shton punonjësit, klientët dhe materialet bazë.
3. Krijon projekte dhe i lidh me klientë / menaxherë.
4. Ndahen detyrat te punonjësit dhe ndiqet statusi i tyre.
5. Nga terreni ngarkohen raporte (projekti ose detyre) me foto.
6. Paralelisht menaxhohen oferta, stoqe, kontrata, pushime dhe lëvizje financiare.
7. Dashboard-i dhe raportet japin pamje të përgjithshme të punës.

---

## Karakteristika teknike (shkurt)

- Aplikacion **Laravel** me pamje **Blade** dhe **Bootstrap**
- Arkitekturë **Controller → Service → Repository**
- Databaza **MySQL** (skema në `prokom.sql`)
- Multi-tenant me izolim sipas kompanisë
- Ngarkim skedarësh (imazhe raportesh, dokumente kontratash) përmes storage

Për instalim dhe setup: shiko [README.md](README.md).
