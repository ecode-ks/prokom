# Prokom

**Prokom** është një platformë multi-tenant për menaxhimin e kompanive të ndërtimit / projekteve: punonjës, projekte, detyra, materiale, oferta, kontrata, pushime dhe raporte.

UI është në **shqip**. Stack: **Laravel** + **Blade** + **MySQL** + **Bootstrap**.

---

## Çfarë bën (MVP)

| Moduli | Funksionaliteti kryesor |
|--------|-------------------------|
| Auth | Login admin / kompani, profil, logout |
| Kompanitë | Regjistrim, lista, status, paketat |
| Dashboard | Pasqyra e përgjithshme |
| Punonjësit | CRUD, role, bllokim/aktivizim |
| Klientët | CRUD |
| Furnitorët | CRUD |
| Materialet / Stoqet | CRUD + raport stoku |
| Ofertat | Krijim, edit, faturë, status |
| Transaksionet | CRUD + raport financiar |
| Kontrata | CRUD + dokumente |
| Pushimet | CRUD + status (aprovim) |
| Projektet | CRUD, detaje, status, kontekst projekti |
| Raportet e Projekteve | CRUD, imazhe, status |
| Detyrat | CRUD, shumë punonjës, status |
| Raportet e Detyrave | CRUD, imazhe, status |
| Orët e Punës / Check-in | Evidenca e orëve dhe check-in |
| Lajmet e Kompanisë | CRUD |

---

## Arkitektura

```
Controller → Service → Repository (DB::table)
Views: resources/views/management/*.blade.php
```

- Çdo kompani ka `company_id` (multi-tenant).
- Role tipike: `admin`, `company`, `manager`, `employee`, `finance`, `hr`.

---

## Kërkesat

- PHP 8.2+
- Composer
- MySQL / MariaDB
- Node.js (për Vite/assets, opsionale)

---

## Instalimi

```bash
# 1. Klono / hap projektin
cd prokom

# 2. Varësitë
composer install
npm install && npm run build

# 3. Environment
cp .env.example .env
php artisan key:generate

# 4. Konfiguro databazën në .env
# DB_DATABASE=prokom
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Importo skemën
# Nga HeidiSQL / phpMyAdmin: importo prokom.sql
# Ose:
mysql -u root -p prokom < prokom.sql

# 6. Storage link (për imazhe/dokumente)
php artisan storage:link

# 7. Nis serverin
php artisan serve
```

Hap: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## Databaza

Skema kryesore: `prokom.sql`

Nëse ke tabela ekzistuese, shiko skriptet në `database/` (p.sh. ALTER për tasks):

- `database/alter_tasks_table.sql`
- `database/alter_tasks_assigned_to_multiple.sql`

---

## Rolet (shkurt)

| Roli | Qasja tipike |
|------|----------------|
| `admin` | Kompanitë, paketat |
| `company` | Menaxhim i plotë i kompanisë |
| `manager` | Projekte, detyra, raporte |
| `employee` | Detyra / raporte (sipas menysë) |
| `finance` | Transaksione, furnitorë |
| `hr` | Punonjës, kontrata, pushime |

---

## Struktura e projektit

```
app/
  Http/Controllers/
  Services/
  Repositories/
  Models/
resources/views/
  management/     # faqet e moduleve
  template/       # layout + sidebar
routes/web.php
prokom.sql
```

---

## Dokumentacioni i MVP

Shiko **[MVP.md](MVP.md)** për:

- qëllimin e produktit
- çfarë përfshihet në MVP
- çfarë mbetet jashtë
- kritere suksesi





---

## Licenca

Projekt privat / i brendshëm (ecode).
