<?php

/*
 * Datenschutzerklärung für workom.co, die WORKOM-Webplattform und die WORKOM-App (co.workom.app).
 * Blöcke: ['p' => Absatz], ['h' => Zwischentitel] oder ['ul' => [Punkte]]. Alle drei Sprachen gleich halten.
 */
return [
    'meta_title' => 'Datenschutzerklärung | WORKOM',
    'meta_description' => 'Wie WORKOM personenbezogene Daten auf der Website, in der Webplattform und in der mobilen App erhebt, nutzt, weitergibt und schützt.',
    'label' => 'Rechtliches',
    'title' => 'Datenschutz­erklärung', // soft hyphen: breaks only on narrow screens
    'updated' => 'Zuletzt aktualisiert: 14. Oktober 2026',
    'intro' => 'Diese Erklärung beschreibt, welche personenbezogenen Daten WORKOM erhebt, warum wir sie erheben, an wen wir sie weitergeben und welche Wahlmöglichkeiten Sie haben. Sie gilt für die Website workom.co, die WORKOM-Webplattform und die WORKOM-App für Android und iOS (co.workom.app).',
    'toc' => 'Auf dieser Seite',
    'contact_cta' => 'Fragen zu Ihren Daten?',

    'sections' => [
        [
            'id' => 'who-we-are',
            'title' => 'Wer wir sind',
            'content' => [
                ['p' => 'WORKOM ist eine Plattform für Unternehmensverwaltung (ERP, CRM und HR), entwickelt und betrieben von eCode Tech („WORKOM“, „wir“).'],
                ['p' => 'Sie erreichen uns unter contact@workom.co oder +383 45 548 747.'],
            ],
        ],
        [
            'id' => 'roles',
            'title' => 'Unternehmen, Mitarbeitende und unsere Rolle',
            'content' => [
                ['p' => 'WORKOM wird von Unternehmen (unseren Kunden) genutzt, um Projekte, Kunden und Personal zu verwalten. Ein Unternehmen legt die Konten seiner Mitarbeitenden an und entscheidet, welche Daten über sie erfasst werden.'],
                ['ul' => [
                    'Für Daten, die ein Unternehmen über seine Mitarbeitenden erfasst (zum Beispiel Anwesenheit, Verträge und Löhne), ist das Unternehmen verantwortlich. Wir verarbeiten diese Daten in seinem Auftrag und nach seinen Weisungen.',
                    'Für das Unternehmenskonto selbst, für unsere Website und für die direkte Kommunikation mit uns ist WORKOM verantwortlich.',
                ]],
                ['p' => 'Wenn Sie WORKOM als Mitarbeitende nutzen, kann Ihr Arbeitgeber Ihre Arbeitsdaten entsprechend der Ihnen zugewiesenen Rolle einsehen und verwalten.'],
            ],
        ],
        [
            'id' => 'data-we-collect',
            'title' => 'Welche Daten wir erheben',
            'content' => [
                ['h' => 'Kontodaten'],
                ['ul' => [
                    'Unternehmenskonten: Firmenname, E-Mail-Adresse, Telefonnummer, Adresse oder Standort, Benutzername und Passwort.',
                    'Mitarbeiterkonten: Name, Telefonnummer, E-Mail-Adresse, Benutzername, Passwort, Rolle und Kontostatus.',
                    'Passwörter werden nur als Hash gespeichert; wir können sie nicht lesen.',
                ]],
                ['h' => 'Anwesenheits- und Standortdaten'],
                ['ul' => [
                    'Wenn Mitarbeitende einstempeln, eine Pause beginnen oder beenden oder ausstempeln, speichern wir den Zeitpunkt und den Standort des Geräts (GPS-Koordinaten) in diesem Moment.',
                    'Der Standort wird nur in diesen Momenten erfasst, wenn die Person die Aktion ausführt. Die App verfolgt den Standort nicht laufend und nicht im Hintergrund.',
                    'Diese Einträge sind für das Unternehmen und seine berechtigten Vorgesetzten sichtbar, auch auf der Anwesenheitskarte.',
                ]],
                ['h' => 'Arbeitsdaten'],
                ['ul' => [
                    'Projekte, Aufgaben, Aufgaben- und Projektberichte, einschließlich des Textes und der Fotos, die Sie anhängen.',
                    'Arbeitsstunden, Überstunden, Urlaubsanträge und Unternehmensmitteilungen.',
                ]],
                ['h' => 'Personal- und Lohndaten, die das Unternehmen erfasst'],
                ['ul' => [
                    'Verträge (Position, Lohn, Vergütungsart, Stunden- und Überstundensätze, Beginn- und Enddatum sowie hochgeladene Vertragsdokumente) und Lohneinträge.',
                ]],
                ['h' => 'Geschäftsdaten, die das Unternehmen erfasst'],
                ['ul' => [
                    'Kunden und Lieferanten (zum Beispiel Namen, Telefonnummern und Standorte), Angebote, Materialien und Finanztransaktionen.',
                ]],
                ['h' => 'Geräte- und technische Daten'],
                ['ul' => [
                    'Ein Push-Benachrichtigungs-Token und die Geräteplattform (Android oder iOS), damit Benachrichtigungen das richtige Gerät erreichen.',
                    'Technische Daten, die für den sicheren Betrieb nötig sind, etwa IP-Adresse, Anfrageprotokolle, Anmelde-Tokens und Sitzungs-Cookies.',
                ]],
                ['h' => 'Abonnementdaten'],
                ['ul' => [
                    'Das genutzte Paket eines Unternehmens, sein Preis und die Zahlungsdaten. In der App erheben wir keine Zahlungskartendaten.',
                ]],
            ],
        ],
        [
            'id' => 'permissions',
            'title' => 'Berechtigungen der mobilen App',
            'content' => [
                ['ul' => [
                    'Standort: wird nur beim Einstempeln, beim Beginn oder Ende einer Pause und beim Ausstempeln genutzt, um festzuhalten, wo die Aktion stattfand.',
                    'Kamera und Fotos: werden nur genutzt, wenn Sie Fotos aufnehmen oder einem Bericht anhängen.',
                    'Benachrichtigungen: informieren Sie über Aufgaben, Freigaben und Unternehmensmitteilungen. Sie können sie in den Geräteeinstellungen deaktivieren.',
                ]],
                ['p' => 'Sie können jede Berechtigung in den Geräteeinstellungen verweigern oder widerrufen. Einige Funktionen, etwa das Einstempeln mit Standort, funktionieren dann möglicherweise nicht.'],
            ],
        ],
        [
            'id' => 'how-we-use',
            'title' => 'Wie wir Daten verwenden',
            'content' => [
                ['ul' => [
                    'Um WORKOM bereitzustellen: Anwesenheit und Arbeitszeiten, Projekte und Berichte, Personal- und Lohnberechnungen, Kunden, Angebote und Finanzberichte.',
                    'Um Konten anzulegen und zu schützen, Benutzer anzumelden und rollenbasierte Berechtigungen anzuwenden.',
                    'Um Benachrichtigungen zu senden, die Ihre Arbeit in WORKOM betreffen.',
                    'Um Abonnements und Zahlungen von Unternehmen zu verwalten.',
                    'Um Support zu leisten, Anfragen zu beantworten und den Dienst sicher und funktionsfähig zu halten.',
                    'Um gesetzliche Pflichten zu erfüllen.',
                ]],
                ['p' => 'Wir verkaufen keine personenbezogenen Daten, nutzen sie nicht für Werbung und zeigen in WORKOM keine Werbung an.'],
            ],
        ],
        [
            'id' => 'sharing',
            'title' => 'An wen wir Daten weitergeben',
            'content' => [
                ['p' => 'Innerhalb eines Unternehmens sind Daten für Benutzer entsprechend der vom Unternehmen vergebenen Rollen sichtbar (zum Beispiel Unternehmensadministrator, Vorgesetzte, HR, Finanzen oder Mitarbeitende).'],
                ['p' => 'Wir setzen einige wenige Dienstleister ein, die Daten in unserem Auftrag verarbeiten:'],
                ['ul' => [
                    'Hosting-Anbieter: speichert die Datenbank und hochgeladene Dateien und betreibt den Dienst.',
                    'Expo (expo.dev): stellt Push-Benachrichtigungen an die mobile App zu, über Google Firebase Cloud Messaging und den Apple Push Notification service. Expo erhält das Push-Token und den Inhalt der Benachrichtigung.',
                    'OpenStreetMap: liefert die Kartenkacheln der Anwesenheitskarte. Ihr Browser lädt sie direkt von deren Servern.',
                    'Google Fonts: liefert die Schriften unserer Website. Ihr Browser lädt sie direkt von Google.',
                ]],
                ['p' => 'Wir können Daten außerdem offenlegen, wenn das Gesetz es verlangt oder um die Rechte und die Sicherheit unserer Nutzer und von WORKOM zu schützen. Darüber hinaus geben wir keine personenbezogenen Daten weiter.'],
            ],
        ],
        [
            'id' => 'retention',
            'title' => 'Wie lange wir Daten speichern',
            'content' => [
                ['p' => 'Wir speichern Daten, solange das Unternehmenskonto aktiv ist. Unternehmen können Mitarbeitende, Stempelungen, Berichte und andere Einträge jederzeit löschen.'],
                ['p' => 'Wird ein Unternehmenskonto geschlossen, löschen oder anonymisieren wir seine Daten innerhalb eines angemessenen Zeitraums, sofern das Gesetz nicht verlangt, bestimmte Unterlagen länger aufzubewahren, etwa Lohn- oder Buchhaltungsunterlagen.'],
            ],
        ],
        [
            'id' => 'security',
            'title' => 'Sicherheit',
            'content' => [
                ['p' => 'Wir schützen Daten durch verschlüsselte Verbindungen (HTTPS), gehashte Passwörter, tokenbasierte Anmeldung für die mobile App und rollenbasierten Zugriff innerhalb jedes Unternehmens. Keine Übertragungs- oder Speichermethode ist vollkommen sicher, aber wir arbeiten daran, Ihre Daten zu schützen und Probleme schnell zu beheben.'],
            ],
        ],
        [
            'id' => 'your-rights',
            'title' => 'Ihre Rechte und Kontolöschung',
            'content' => [
                ['p' => 'Je nach geltendem Recht können Sie Auskunft, Berichtigung oder Löschung Ihrer personenbezogenen Daten verlangen oder der Verarbeitung widersprechen bzw. sie einschränken lassen.'],
                ['ul' => [
                    'Mitarbeitende: Ihr Konto wird von Ihrem Arbeitgeber verwaltet. Wenden Sie sich zuerst an Ihren Arbeitgeber; Sie können uns auch unter contact@workom.co schreiben, und wir helfen Ihnen oder leiten Ihre Anfrage weiter.',
                    'Unternehmen: Sie können Mitarbeitende und Einträge in WORKOM bearbeiten oder löschen. Um Ihr Unternehmenskonto zu schließen und seine Daten löschen zu lassen, schreiben Sie an contact@workom.co von der im Konto hinterlegten E-Mail-Adresse.',
                ]],
                ['p' => 'Wir beantworten Anfragen innerhalb von 30 Tagen.'],
            ],
        ],
        [
            'id' => 'website',
            'title' => 'Unsere Website',
            'content' => [
                ['p' => 'workom.co verwendet nur Cookies, die für den Betrieb notwendig sind, etwa das Sitzungs-Cookie und Ihre Sprachwahl. Es werden keine Analyse- oder Werbe-Cookies verwendet. Damit die Seite schneller lädt und auch offline verfügbar ist, kann Ihr Browser Kopien unserer öffentlichen Seiten auf Ihrem Gerät speichern.'],
            ],
        ],
        [
            'id' => 'children',
            'title' => 'Kinder',
            'content' => [
                ['p' => 'WORKOM ist ein Werkzeug für Unternehmen und nicht für Kinder unter 16 Jahren bestimmt. Wir erheben wissentlich keine Daten von Kindern.'],
            ],
        ],
        [
            'id' => 'transfers',
            'title' => 'Internationale Übermittlungen',
            'content' => [
                ['p' => 'Einige unserer Dienstleister, etwa Expo, können Daten außerhalb des Kosovo verarbeiten. In diesen Fällen setzen wir auf Anbieter, die geeignete Schutzmaßnahmen für personenbezogene Daten anwenden.'],
            ],
        ],
        [
            'id' => 'changes',
            'title' => 'Änderungen dieser Erklärung',
            'content' => [
                ['p' => 'Wir können diese Erklärung aktualisieren, wenn sich WORKOM ändert. Die neue Fassung veröffentlichen wir auf dieser Seite und aktualisieren das Datum oben. Bei wesentlichen Änderungen informieren wir auch die Unternehmen, die WORKOM nutzen.'],
            ],
        ],
        [
            'id' => 'contact',
            'title' => 'Kontakt',
            'content' => [
                ['p' => 'Bei Fragen zum Datenschutz oder zu Ihren Daten erreichen Sie uns unter contact@workom.co oder +383 45 548 747.'],
            ],
        ],
    ],
];
