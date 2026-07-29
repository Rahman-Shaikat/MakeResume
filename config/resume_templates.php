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

$catalog['template-two'] = [
    'name' => 'Classic Blue Sidebar',
    'description' => 'A refined engineering resume with a spacious experience column and structured blue sidebar.',
    'sample' => array_replace($catalog['template-one']['sample'], [
        'title' => 'Software Engineer',
        'phone' => '+44 1234567890',
        'location' => 'Bristol, United Kingdom',
        'summary' => 'Software engineer focused on building reliable products, improving system quality, and translating complex requirements into maintainable applications.',
    ]),
];

$catalog['template-three'] = [
    'name' => 'Modern Mint Professional',
    'description' => 'A confident two-column resume with mint highlights, icon-led sections, and detailed experience.',
    'sample' => array_replace($catalog['template-one']['sample'], [
        'title' => 'Research Software Engineer · Healthcare Software Advocate',
        'phone' => '+1 (234) 555-1234',
        'location' => 'Fort Worth, Texas',
        'linkedin' => 'https://linkedin.com/in/your-profile',
        'github' => '',
        'summary' => 'With 10 years of experience in software development, specializing in healthcare data applications, I have a proven track record of improving patient management systems and reducing operational costs. Proficient in Python and Java, I combine practical engineering leadership with a dedication to advancing healthcare software technology.',
        'skills' => ['Python', 'Java', 'Machine Learning', 'Cloud Computing', 'Data Visualization', 'C++'],
        'experience' => [
            [
                'role' => 'Senior Software Engineer',
                'company' => 'HealthTech Solutions',
                'dates' => '02/2025 - Present',
                'location' => 'Dallas, Texas',
                'url' => '',
                'intro' => '',
                'highlights' => [
                    'Led development of a patient management system that reduced administrative workload by 25% using Python and AWS.',
                    'Collaborated with data scientists to integrate machine learning models, improving diagnostic accuracy.',
                    'Optimized cloud-based data processing workflows and mentored engineers through complex releases.',
                ],
            ],
            [
                'role' => 'Software Engineer',
                'company' => 'Medico Systems',
                'dates' => '06/2021 - 01/2025',
                'location' => 'Remote + Austin, Texas',
                'url' => '',
                'intro' => '',
                'highlights' => [
                    'Developed scalable healthcare applications using Java and Python, reducing infrastructure costs.',
                    'Collaborated with global teams to improve data security and project delivery.',
                    'Enhanced the existing codebase with efficient algorithms and dependable automated tests.',
                ],
            ],
            [
                'role' => 'Software Developer',
                'company' => 'TechMed Solutions',
                'dates' => '07/2016 - 05/2021',
                'location' => 'Austin, Texas',
                'url' => '',
                'intro' => '',
                'highlights' => [
                    'Implemented C++ data models for healthcare applications and visualization tools.',
                    'Streamlined deployment processes while improving delivery quality and stakeholder satisfaction.',
                ],
            ],
        ],
        'education' => [
            [
                'degree' => 'Bachelor of Science in Computer Science',
                'school' => 'The University of Texas at Austin',
                'year' => '01/2012 - 01/2016',
                'location' => 'Austin, Texas',
            ],
        ],
        'awards' => [
            [
                'title' => 'Innovative Software Development Award',
                'description' => 'Recognized for developing a system that improved hospital operational efficiency.',
            ],
            [
                'title' => 'Top Performer in Software Engineering',
                'description' => 'Recognized for exceptional contributions to a complex healthcare platform.',
            ],
        ],
        'interest' => [
            'title' => 'Healthcare Technology',
            'description' => 'Passionate about creating software solutions that significantly improve healthcare delivery.',
        ],
    ]),
];

$catalog['template-four'] = [
    'name' => 'Teal Impact',
    'description' => 'A bold full-height teal sidebar paired with a spacious, achievement-focused professional layout.',
    'sample' => array_replace($catalog['template-one']['sample'], [
        'title' => 'Sr. Software Engineer | Full-Stack Development | Cloud Solutions',
        'phone' => '+1 (234) 555-1234',
        'location' => 'San Jose, California',
        'linkedin' => 'https://linkedin.com/in/your-profile',
        'github' => '',
        'summary' => 'With over 3 years of professional experience, I am driven to leverage my expertise in full-stack development, front-end technologies, and cloud solutions to create product innovation. My career highlight includes leading a project that boosted user engagement by 20%.',
        'skills' => ['HTML', 'CSS', 'JavaScript', 'React', 'TypeScript', 'Java', 'AWS', 'Docker'],
        'experience' => [
            [
                'role' => 'Senior Full-Stack Developer',
                'company' => 'Tech Innovations Inc',
                'dates' => '01/2021 - Present',
                'location' => 'San Jose, CA',
                'url' => '',
                'intro' => '',
                'highlights' => [
                    'Spearheaded development of a feature-rich analytics platform, improving customer insights.',
                    'Orchestrated a seamless migration of key applications to AWS, reducing hosting costs.',
                    'Championed CI/CD processes using Jenkins and Docker for consistent production updates.',
                    'Directed and supported junior developers while improving code quality and maintainability.',
                ],
            ],
            [
                'role' => 'Software Engineer II',
                'company' => 'CodeCrafters International',
                'dates' => '09/2018 - 12/2020',
                'location' => 'Mountain View, CA',
                'url' => '',
                'intro' => '',
                'highlights' => [
                    'Developed an e-commerce application using React and TypeScript.',
                    'Enhanced authentication and security by implementing OAuth and JWT.',
                    'Optimized database queries and mentored junior developers in test-driven development.',
                ],
            ],
            [
                'role' => 'Software Developer',
                'company' => 'NextGen Solutions',
                'dates' => '06/2016 - 08/2018',
                'location' => 'Palo Alto, CA',
                'url' => '',
                'intro' => '',
                'highlights' => [
                    'Implemented cloud-based SaaS features for enterprise customers.',
                    'Reduced application load time and introduced a company-wide code review practice.',
                ],
            ],
        ],
        'education' => [
            [
                'degree' => "Master's in Computer Science",
                'school' => 'Stanford University',
                'year' => '01/2014 - 01/2016',
                'location' => 'Stanford, CA',
            ],
            [
                'degree' => "Bachelor's in Software Engineering",
                'school' => 'San Jose State University',
                'year' => '01/2010 - 01/2014',
                'location' => 'San Jose, CA',
            ],
        ],
        'projects' => [
            [
                'name' => 'Open Source Contribution to ChatEngine',
                'url' => 'https://github.com/ChatEngine',
                'description' => 'Enhanced real-time chat capabilities by integrating WebSockets for broader browser support.',
            ],
            [
                'name' => 'Development of MiniCRM System',
                'url' => 'https://github.com/MiniCRM',
                'description' => 'Built a lightweight CRM platform for small businesses using Node.js and MongoDB.',
            ],
        ],
        'awards' => [
            [
                'title' => 'Lead Project to Boost Engagement',
                'description' => 'Led a key project that resulted in a 20% increase in user engagement.',
            ],
            [
                'title' => 'Recognized for Optimizing Costs',
                'description' => 'Drove a cloud migration initiative that cut hosting expenses.',
            ],
            [
                'title' => 'Mentorship Excellence Award',
                'description' => 'Recognized for mentoring junior staff and boosting team performance.',
            ],
        ],
        'course' => [
            'name' => 'Advanced React and Redux',
            'provider' => 'Udemy',
            'description' => 'Detailed study of React, Redux, and React Router for scalable web applications.',
        ],
    ]),
];

return ['catalog' => $catalog];
