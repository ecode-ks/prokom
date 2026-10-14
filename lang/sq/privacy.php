<?php

/*
 * Politika e privatësisë për workom.co, platformën web WORKOM dhe aplikacionin mobil WORKOM (co.workom.app).
 * Blloqet: ['p' => paragraf], ['h' => nëntitull] ose ['ul' => [pika]]. Mbajini të tri gjuhët të njëjta.
 */
return [
    'meta_title' => 'Politika e privatësisë | WORKOM',
    'meta_description' => 'Si i mbledh, përdor, ndan dhe mbron WORKOM të dhënat personale në website, platformën web dhe aplikacionin mobil.',
    'label' => 'Ligjore',
    'title' => 'Politika e privatësisë',
    'updated' => 'Përditësuar më: 14 tetor 2026',
    'intro' => 'Kjo politikë shpjegon cilat të dhëna personale mbledh WORKOM, pse i mbledhim, me kë i ndajmë dhe çfarë zgjedhjesh keni. Ajo vlen për website-in workom.co, platformën web WORKOM dhe aplikacionin mobil WORKOM për Android dhe iOS (co.workom.app).',
    'toc' => 'Në këtë faqe',
    'contact_cta' => 'Keni pyetje për të dhënat tuaja?',

    'sections' => [
        [
            'id' => 'who-we-are',
            'title' => 'Kush jemi',
            'content' => [
                ['p' => 'WORKOM është platformë për menaxhimin e biznesit (ERP, CRM dhe HR), e zhvilluar dhe e operuar nga eCode Tech ("WORKOM", "ne").'],
                ['p' => 'Na kontaktoni në contact@workom.co ose +383 45 548 747.'],
            ],
        ],
        [
            'id' => 'roles',
            'title' => 'Kompanitë, punonjësit dhe roli ynë',
            'content' => [
                ['p' => 'WORKOM përdoret nga kompanitë (klientët tanë) për të menaxhuar projektet, klientët dhe stafin. Kompania krijon llogaritë për punonjësit e saj dhe vendos cilat të dhëna futen për ta.'],
                ['ul' => [
                    'Për të dhënat që kompania fut ose mbledh për punonjësit e saj (p.sh. prezencën, kontratat dhe pagat), përgjegjëse është kompania. Ne i përpunojmë ato në emër të saj dhe sipas udhëzimeve të saj.',
                    'Për llogarinë e kompanisë, për website-in tonë dhe për komunikimin e drejtpërdrejtë me ne, përgjegjës për të dhënat është WORKOM.',
                ]],
                ['p' => 'Nëse e përdorni WORKOM si punonjës, punëdhënësi juaj mund t\'i shohë dhe t\'i menaxhojë të dhënat tuaja të punës sipas rolit që ju ka caktuar.'],
            ],
        ],
        [
            'id' => 'data-we-collect',
            'title' => 'Të dhënat që mbledhim',
            'content' => [
                ['h' => 'Të dhënat e llogarisë'],
                ['ul' => [
                    'Llogaritë e kompanive: emri i kompanisë, adresa e email-it, numri i telefonit, adresa ose lokacioni, emri i përdoruesit dhe fjalëkalimi.',
                    'Llogaritë e punonjësve: emri, numri i telefonit, adresa e email-it, emri i përdoruesit, fjalëkalimi, roli dhe statusi i llogarisë.',
                    'Fjalëkalimet ruhen vetëm në formë të koduar (hash); ne nuk mund t\'i lexojmë.',
                ]],
                ['h' => 'Të dhënat e prezencës dhe lokacionit'],
                ['ul' => [
                    'Kur punonjësi bën check-in, fillon ose mbaron pauzën, ose bën check-out, ne regjistrojmë kohën dhe lokacionin e pajisjes (koordinatat GPS) në atë moment.',
                    'Lokacioni merret vetëm në këto momente, kur punonjësi kryen veprimin. Aplikacioni nuk e ndjek lokacionin vazhdimisht dhe as në sfond.',
                    'Këto regjistrime u shfaqen kompanisë dhe menaxherëve të autorizuar, përfshirë hartën e prezencës.',
                ]],
                ['h' => 'Të dhënat e punës'],
                ['ul' => [
                    'Projektet, detyrat, raportet e detyrave dhe të projekteve, përfshirë tekstin dhe fotot që zgjidhni t\'i bashkëngjitni.',
                    'Orët e punës, orët shtesë, kërkesat për pushim dhe lajmet e kompanisë.',
                ]],
                ['h' => 'Të dhënat e HR-it dhe pagave që i fut kompania'],
                ['ul' => [
                    'Kontratat (pozita, paga, lloji i pagesës, tarifat për orë dhe orë shtesë, data e fillimit dhe e mbarimit, dokumentet e ngarkuara) dhe regjistrimet e pagave.',
                ]],
                ['h' => 'Të dhënat e biznesit që i fut kompania'],
                ['ul' => [
                    'Klientët dhe furnizuesit (p.sh. emrat, numrat e telefonit dhe lokacionet), ofertat, materialet dhe transaksionet financiare.',
                ]],
                ['h' => 'Të dhënat e pajisjes dhe teknike'],
                ['ul' => [
                    'Token-i për njoftime push dhe platforma e pajisjes (Android ose iOS), që njoftimet të arrijnë te pajisja e duhur.',
                    'Të dhëna teknike që nevojiten për funksionimin e sigurt të shërbimit, si adresa IP, regjistrat e kërkesave, token-ët e hyrjes dhe cookies të sesionit.',
                ]],
                ['h' => 'Të dhënat e abonimit'],
                ['ul' => [
                    'Paketa që përdor kompania, çmimi dhe datat e pagesave. Në aplikacion nuk mbledhim të dhëna të kartave të pagesës.',
                ]],
            ],
        ],
        [
            'id' => 'permissions',
            'title' => 'Lejet e aplikacionit mobil',
            'content' => [
                ['ul' => [
                    'Lokacioni: përdoret vetëm kur punonjësi bën check-in, fillon ose mbaron pauzën, ose bën check-out, për të regjistruar ku u krye veprimi.',
                    'Kamera dhe fotot: përdoren vetëm kur zgjidhni të bëni ose të bashkëngjitni foto në një raport.',
                    'Njoftimet: përdoren për t\'ju njoftuar për detyra, miratime dhe lajme të kompanisë. Mund t\'i çaktivizoni në cilësimet e pajisjes.',
                ]],
                ['p' => 'Çdo leje mund ta refuzoni ose ta tërhiqni në cilësimet e pajisjes. Disa funksione, si check-in me lokacion, mund të mos punojnë atëherë.'],
            ],
        ],
        [
            'id' => 'how-we-use',
            'title' => 'Si i përdorim të dhënat',
            'content' => [
                ['ul' => [
                    'Për të ofruar WORKOM: prezencën dhe orët e punës, projektet dhe raportet, llogaritjet e HR-it dhe pagave, klientët, ofertat dhe raportet financiare.',
                    'Për të krijuar dhe mbrojtur llogaritë, për hyrjen e përdoruesve dhe për zbatimin e lejeve sipas roleve.',
                    'Për të dërguar njoftime që lidhen me punën tuaj në WORKOM.',
                    'Për të menaxhuar abonimet dhe pagesat e kompanive.',
                    'Për mbështetje, përgjigje ndaj kërkesave dhe për ta mbajtur shërbimin të sigurt dhe funksional.',
                    'Për të përmbushur detyrimet ligjore.',
                ]],
                ['p' => 'Ne nuk i shesim të dhënat personale, nuk i përdorim për reklama dhe nuk shfaqim reklama në WORKOM.'],
            ],
        ],
        [
            'id' => 'sharing',
            'title' => 'Me kë i ndajmë të dhënat',
            'content' => [
                ['p' => 'Brenda kompanisë, të dhënat u shfaqen përdoruesve sipas roleve që cakton kompania (p.sh. administrator i kompanisë, menaxher, HR, financë ose punonjës).'],
                ['p' => 'Përdorim një numër të vogël ofruesish shërbimesh që i përpunojnë të dhënat për ne:'],
                ['ul' => [
                    'Ofruesi i hostingut: ruan databazën dhe fajllat e ngarkuar dhe e mban shërbimin në punë.',
                    'Expo (expo.dev): dërgon njoftimet push në aplikacionin mobil, përmes Google Firebase Cloud Messaging dhe Apple Push Notification service. Merr token-in e njoftimeve dhe përmbajtjen e njoftimit.',
                    'OpenStreetMap: ofron pllakat e hartës që shfaqen në hartën e prezencës. Shfletuesi juaj i kërkon ato drejtpërdrejt nga serverët e tyre.',
                    'Google Fonts: ofron fontet e përdorura në website-in tonë. Shfletuesi juaj i ngarkon drejtpërdrejt nga Google.',
                ]],
                ['p' => 'Mund t\'i zbulojmë të dhënat edhe kur e kërkon ligji ose për të mbrojtur të drejtat dhe sigurinë e përdoruesve tanë dhe të WORKOM. Të dhënat personale nuk i ndajmë me askënd tjetër.'],
            ],
        ],
        [
            'id' => 'retention',
            'title' => 'Sa kohë i ruajmë të dhënat',
            'content' => [
                ['p' => 'Të dhënat i ruajmë për aq kohë sa llogaria e kompanisë është aktive. Kompanitë mund t\'i fshijnë punonjësit, check-in-et, raportet dhe regjistrimet e tjera në çdo kohë.'],
                ['p' => 'Kur mbyllet llogaria e një kompanie, i fshijmë ose i anonimizojmë të dhënat e saj brenda një periudhe të arsyeshme, përveç rasteve kur ligji kërkon që disa regjistrime të ruhen më gjatë, p.sh. regjistrimet e pagave ose të kontabilitetit.'],
            ],
        ],
        [
            'id' => 'security',
            'title' => 'Siguria',
            'content' => [
                ['p' => 'Të dhënat i mbrojmë me lidhje të enkriptuara (HTTPS), fjalëkalime të koduara (hash), autentikim me token për aplikacionin mobil dhe qasje sipas roleve brenda çdo kompanie. Asnjë metodë transmetimi apo ruajtjeje nuk është plotësisht e sigurt, por punojmë për t\'i mbrojtur të dhënat tuaja dhe për t\'i rregulluar problemet shpejt.'],
            ],
        ],
        [
            'id' => 'your-rights',
            'title' => 'Të drejtat tuaja dhe fshirja e llogarisë',
            'content' => [
                ['p' => 'Varësisht nga ligji që vlen për ju, mund të kërkoni qasje, korrigjim ose fshirje të të dhënave tuaja personale, ose të kundërshtoni apo kufizoni përpunimin e tyre.'],
                ['ul' => [
                    'Punonjësit: llogaria juaj menaxhohet nga punëdhënësi. Drejtojuni së pari punëdhënësit; mund të na shkruani edhe ne në contact@workom.co dhe do t\'ju ndihmojmë ose do ta përcjellim kërkesën.',
                    'Kompanitë: mund t\'i ndryshoni ose t\'i fshini punonjësit dhe regjistrimet në WORKOM. Për ta mbyllur llogarinë e kompanisë dhe për t\'i fshirë të dhënat e saj, na shkruani në contact@workom.co nga adresa e email-it e regjistruar në llogari.',
                ]],
                ['p' => 'Kërkesave u përgjigjemi brenda 30 ditëve.'],
            ],
        ],
        [
            'id' => 'website',
            'title' => 'Website-i ynë',
            'content' => [
                ['p' => 'workom.co përdor vetëm cookies që nevojiten për funksionimin e tij, si cookie-n e sesionit dhe zgjedhjen e gjuhës. Nuk përdor cookies për analitikë apo reklama. Që faqja të jetë më e shpejtë dhe e qasshme edhe pa internet, shfletuesi mund të ruajë kopje të faqeve tona publike në pajisjen tuaj.'],
            ],
        ],
        [
            'id' => 'children',
            'title' => 'Fëmijët',
            'content' => [
                ['p' => 'WORKOM është mjet pune dhe nuk është i dedikuar për fëmijë nën 16 vjeç. Ne nuk mbledhim me vetëdije të dhëna nga fëmijët.'],
            ],
        ],
        [
            'id' => 'transfers',
            'title' => 'Transferimet ndërkombëtare',
            'content' => [
                ['p' => 'Disa nga ofruesit tanë të shërbimeve, si Expo, mund t\'i përpunojnë të dhënat jashtë Kosovës. Në këto raste mbështetemi te ofrues që zbatojnë masa të përshtatshme për mbrojtjen e të dhënave personale.'],
            ],
        ],
        [
            'id' => 'changes',
            'title' => 'Ndryshimet e kësaj politike',
            'content' => [
                ['p' => 'Mund ta përditësojmë këtë politikë kur ndryshon WORKOM. Versionin e ri do ta publikojmë në këtë faqe dhe do ta ndryshojmë datën në krye. Nëse ndryshimet janë të rëndësishme, do t\'i njoftojmë edhe kompanitë që përdorin WORKOM.'],
            ],
        ],
        [
            'id' => 'contact',
            'title' => 'Kontakti',
            'content' => [
                ['p' => 'Për çdo pyetje rreth privatësisë ose të dhënave tuaja, na kontaktoni në contact@workom.co ose +383 45 548 747.'],
            ],
        ],
    ],
];
