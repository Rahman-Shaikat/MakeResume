<?php

declare(strict_types=1);

$catalog = [
    'template-one' => [
        'name' => 'Professional Cyan',
        'description' => 'A crisp two-column layout for developers and technical professionals.',
        'sample' => [
            'title' => 'Laravel Web Application Developer',
            'phone' => '+880 1736 769157',
            'location' => 'Mirpur DOHS, Dhaka, Bangladesh',
            'linkedin' => 'https://www.linkedin.com/in/your-profile',
            'github' => 'https://github.com/your-profile',
            'summary' => 'A software engineer who genuinely enjoys the puzzle of complex problems. My goal is to write clean, reliable code that makes life easier for the end user. I aim to contribute meaningful impact to a forward-thinking team through technical excellence and a commitment to steady growth.',
            'skills' => ['PHP', 'Laravel', 'HTML', 'CSS', 'JavaScript', 'jQuery', 'Git', 'GitHub', 'MySQL', 'RESTful APIs', 'AJAX', 'Redis'],
            'experience' => [
                [
                    'role' => 'Software Engineer',
                    'company' => 'eMythMakers',
                    'dates' => '10/2023 - Present',
                    'location' => '24 Uttar Kafrul, 3rd Floor, Dhaka-1206, Bangladesh',
                    'url' => 'https://www.emythmakers.com',
                    'intro' => 'Company provides web applications and custom software solutions to local and international clients.',
                    'highlights' => [
                        'Handled backend development across 15+ projects, delivering scalable, maintainable APIs, optimizing code performance, implementing caching and consistently maintaining Git version control workflows.',
                        'Working with relational databases, partitions, and model relationships.',
                        'Managing server configuration, deployment, and production issues when required.',
                    ],
                ],
                [
                    'role' => 'Trainee Laravel Developer',
                    'company' => 'iNiLabs',
                    'dates' => '06/2023 - 09/2023',
                    'location' => 'Mirpur-2, Dhaka 1216',
                    'url' => 'https://inilabs.net',
                    'intro' => 'Company focuses on providing SaaS solutions.',
                    'highlights' => [
                        'Assisted in developing and debugging web applications.',
                        'Completed small modules and bug fixes for client projects under senior developer guidance.',
                    ],
                ],
            ],
            'education' => [
                ['degree' => 'Bachelor of Computer Science and Engineering (BCSE)', 'school' => 'IUBAT - International University of Business Agriculture and Technology', 'year' => '2023', 'location' => 'Sector-10, Uttara Model Town, Dhaka-1230, Bangladesh'],
                ['degree' => 'Higher Secondary School Certificate (HSC)', 'school' => 'Shaheed Ramiz Uddin Cantonment College', 'year' => '2016', 'location' => 'Dhaka Cantonment, Dhaka-1206, Bangladesh'],
                ['degree' => 'Secondary School Certificate (SSC)', 'school' => 'Civil Aviation High School Tejgaon', 'year' => '2014', 'location' => 'Tejgaon, Dhaka-1215, Bangladesh'],
            ],
            'projects' => [
                ['name' => 'bdnews24.com', 'url' => 'https://bdnews24.com', 'description' => 'Developed the CMS, Frontend - English & Bangla versions and still maintaining the country’s first and largest online news portal.'],
                ['name' => 'Sanitation Knowledge Repository', 'url' => 'https://www.sanirepo.com', 'description' => 'Built a platform similar to Gates Open Research for sharing research and publications for ITN-BUET.'],
                ['name' => 'Anannya Prokashoni', 'url' => 'https://anannyabooks.com', 'description' => 'Worked on development of this online bookstore, for a popular publication in Bangladesh, integrating secure payments and more efficient content management.'],
                ['name' => 'Amader Dhaka', 'url' => 'https://www.amaderdhaka.info', 'description' => 'Solely responsible for the development of this project, delivering both frontend and backend functionality for a platform that enables Dhaka city residents to submit complaints related to issues in their local areas.'],
                ['name' => 'Step Footwear Ecommerce', 'url' => 'https://stepfootwear.com', 'description' => 'Developed this e-commerce platform for a renowned footwear brand in Bangladesh, implementing backend functionality, product management, and seamless user experience.'],
            ],
            'course' => ['name' => 'PHP with Laravel Framework', 'provider' => 'BASIS'],
            'languages' => [
                ['name' => 'Bangla', 'level' => 'Native', 'percent' => 100],
                ['name' => 'English', 'level' => 'Proficient', 'percent' => 80],
            ],
        ],
    ],
];

$catalog['temp-1'] = [
    'name' => 'Classic Blue Sidebar',
    'description' => 'A refined engineering resume with a spacious experience column and structured blue sidebar.',
    'sample' => array_replace($catalog['template-one']['sample'], [
        'title' => 'Software Engineer',
        'phone' => '+44 1234567890',
        'location' => 'Bristol, United Kingdom',
        'summary' => 'Software engineer focused on building reliable products, improving system quality, and translating complex requirements into maintainable applications.',
    ]),
];

return ['catalog' => $catalog];
