<?php

/*
 * Privacy policy for workom.co, the WORKOM web platform and the WORKOM mobile app (co.workom.app).
 * Blocks: ['p' => paragraph], ['h' => subheading] or ['ul' => [items]]. Keep the three languages in sync.
 */
return [
    'meta_title' => 'Privacy Policy | WORKOM',
    'meta_description' => 'How WORKOM collects, uses, shares and protects personal data in the WORKOM website, web platform and mobile app.',
    'label' => 'Legal',
    'title' => 'Privacy Policy',
    'updated' => 'Last updated: 14 October 2026',
    'intro' => 'This policy explains what personal data WORKOM collects, why we collect it, who we share it with and what choices you have. It applies to the website workom.co, the WORKOM web platform and the WORKOM mobile app for Android and iOS (co.workom.app).',
    'toc' => 'On this page',
    'contact_cta' => 'Questions about your data?',

    'sections' => [
        [
            'id' => 'who-we-are',
            'title' => 'Who we are',
            'content' => [
                ['p' => 'WORKOM is a business management platform (ERP, CRM and HR) developed and operated by eCode Tech ("WORKOM", "we", "us").'],
                ['p' => 'You can reach us at contact@workom.co or +383 45 548 747.'],
            ],
        ],
        [
            'id' => 'roles',
            'title' => 'Companies, employees and our role',
            'content' => [
                ['p' => 'WORKOM is used by companies (our customers) to manage their projects, clients and staff. A company creates accounts for its employees and decides what information is entered about them.'],
                ['ul' => [
                    'For data that a company enters or collects about its employees (for example attendance, contracts and salaries), the company is responsible for that data and we process it on the company\'s behalf and according to its instructions.',
                    'For the company account itself, for our website and for direct communication with us, WORKOM is responsible for the data.',
                ]],
                ['p' => 'If you use WORKOM as an employee, your employer can see and manage your work data according to the role it has given you.'],
            ],
        ],
        [
            'id' => 'data-we-collect',
            'title' => 'Data we collect',
            'content' => [
                ['h' => 'Account data'],
                ['ul' => [
                    'Company accounts: company name, email address, phone number, address or location, username and password.',
                    'Employee accounts: name, phone number, email address, username, password, role and account status.',
                    'Passwords are stored only in hashed form; we cannot read them.',
                ]],
                ['h' => 'Attendance and location data'],
                ['ul' => [
                    'When an employee checks in, starts or ends a break, or checks out, we record the time and the location of the device (GPS coordinates) at that moment.',
                    'Location is collected only at these moments, when the employee performs the action. The app does not track location continuously or in the background.',
                    'These records are shown to the company and its authorised managers, including on the attendance map.',
                ]],
                ['h' => 'Work data'],
                ['ul' => [
                    'Projects, tasks, task and project reports, including the text and the photos you choose to attach.',
                    'Working hours, overtime, vacation requests and company announcements.',
                ]],
                ['h' => 'HR and payroll data entered by the company'],
                ['ul' => [
                    'Contracts (position, salary, pay type, hourly and overtime rates, start and end dates and uploaded contract documents) and salary records.',
                ]],
                ['h' => 'Business data entered by the company'],
                ['ul' => [
                    'Clients and suppliers (for example names, phone numbers and locations), offers, materials and financial transactions.',
                ]],
                ['h' => 'Device and technical data'],
                ['ul' => [
                    'A push notification token and the device platform (Android or iOS), so we can send notifications to the right device.',
                    'Technical data needed to run the service securely, such as IP address, request logs, login tokens and session cookies.',
                ]],
                ['h' => 'Subscription data'],
                ['ul' => [
                    'The package a company uses, its price and payment dates. We do not collect payment card details in the app.',
                ]],
            ],
        ],
        [
            'id' => 'permissions',
            'title' => 'Mobile app permissions',
            'content' => [
                ['ul' => [
                    'Location: used only when an employee checks in, starts or ends a break, or checks out, to record where the action took place.',
                    'Camera and photos: used only when you choose to take or attach photos to a report.',
                    'Notifications: used to tell you about tasks, approvals and company announcements. You can turn them off in your device settings.',
                ]],
                ['p' => 'You can refuse or withdraw any permission in your device settings. Some features, such as checking in with location, may then not work.'],
            ],
        ],
        [
            'id' => 'how-we-use',
            'title' => 'How we use data',
            'content' => [
                ['ul' => [
                    'To provide WORKOM: attendance and working hours, projects and reports, HR and payroll calculations, clients, offers and financial reports.',
                    'To create and secure accounts, sign users in and apply role-based permissions.',
                    'To send notifications that relate to your work in WORKOM.',
                    'To manage company subscriptions and payments.',
                    'To provide support, answer requests and keep the service secure and working.',
                    'To comply with legal obligations.',
                ]],
                ['p' => 'We do not sell personal data, we do not use it for advertising and we do not show ads in WORKOM.'],
            ],
        ],
        [
            'id' => 'sharing',
            'title' => 'Who we share data with',
            'content' => [
                ['p' => 'Inside a company, data is visible to users according to the roles the company assigns (for example company administrator, manager, HR, finance or employee).'],
                ['p' => 'We use a small number of service providers that process data for us:'],
                ['ul' => [
                    'Hosting provider: stores the database and uploaded files and runs the service.',
                    'Expo (expo.dev): delivers push notifications to the mobile app, through Google Firebase Cloud Messaging and Apple Push Notification service. It receives the push token and the content of the notification.',
                    'OpenStreetMap: provides the map tiles shown on the attendance map. Your browser requests the map tiles directly from their servers.',
                    'Google Fonts: provides the fonts used on our website. Your browser loads them directly from Google.',
                ]],
                ['p' => 'We may also disclose data when required by law or to protect the rights and safety of our users and of WORKOM. We do not share personal data with anyone else.'],
            ],
        ],
        [
            'id' => 'retention',
            'title' => 'How long we keep data',
            'content' => [
                ['p' => 'We keep data for as long as the company account is active. Companies can delete employees, check-ins, reports and other records at any time.'],
                ['p' => 'When a company account is closed, we delete or anonymise its data within a reasonable period, unless the law requires us to keep certain records longer, for example payroll or accounting records.'],
            ],
        ],
        [
            'id' => 'security',
            'title' => 'Security',
            'content' => [
                ['p' => 'We protect data with encrypted connections (HTTPS), hashed passwords, token-based authentication for the mobile app and role-based access inside each company. No method of transmission or storage is completely secure, but we work to protect your data and fix problems quickly.'],
            ],
        ],
        [
            'id' => 'your-rights',
            'title' => 'Your rights and account deletion',
            'content' => [
                ['p' => 'Depending on the law that applies to you, you can ask to access, correct or delete your personal data, or object to or restrict its processing.'],
                ['ul' => [
                    'Employees: your account is managed by your employer. Ask your employer first; you can also write to us at contact@workom.co and we will help or forward your request.',
                    'Companies: you can edit or delete your employees and records in WORKOM. To close your company account and delete its data, write to contact@workom.co from the email address registered on the account.',
                ]],
                ['p' => 'We answer requests within 30 days.'],
            ],
        ],
        [
            'id' => 'website',
            'title' => 'Our website',
            'content' => [
                ['p' => 'workom.co uses only cookies that are necessary for it to work, such as the session cookie and your language choice. It uses no analytics or advertising cookies. To make the site faster and available offline, your browser may store copies of our public pages on your device.'],
            ],
        ],
        [
            'id' => 'children',
            'title' => 'Children',
            'content' => [
                ['p' => 'WORKOM is a business tool and is not intended for children under 16. We do not knowingly collect data from children.'],
            ],
        ],
        [
            'id' => 'transfers',
            'title' => 'International transfers',
            'content' => [
                ['p' => 'Some of our service providers, such as Expo, may process data outside Kosovo. When this happens, we rely on providers that apply appropriate safeguards to protect personal data.'],
            ],
        ],
        [
            'id' => 'changes',
            'title' => 'Changes to this policy',
            'content' => [
                ['p' => 'We may update this policy when WORKOM changes. We will publish the new version on this page and update the date at the top. If the changes are significant, we will also inform companies using WORKOM.'],
            ],
        ],
        [
            'id' => 'contact',
            'title' => 'Contact',
            'content' => [
                ['p' => 'For any question about privacy or your data, contact us at contact@workom.co or +383 45 548 747.'],
            ],
        ],
    ],
];
