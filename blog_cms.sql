-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 24, 2024 at 03:43 PM
-- Server version: 8.0.32
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `blog_cms`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int NOT NULL,
  `category_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(19, 'art_and_design_blogs'),
(18, 'lifestyle'),
(17, 'news_blogs');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int NOT NULL,
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  `comment_text` text COLLATE utf8mb4_general_ci NOT NULL,
  `creation_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `moderation_status` enum('pending','approved','rejected') COLLATE utf8mb4_general_ci DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `post_id`, `user_id`, `comment_text`, `creation_date`, `moderation_status`) VALUES
(12, 36, 6, 'Nice', '2024-11-24 04:46:39', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `post_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `body_text` text COLLATE utf8mb4_general_ci NOT NULL,
  `summary` text COLLATE utf8mb4_general_ci,
  `author_id` int NOT NULL,
  `creation_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `publish_status` enum('draft','pending','published') COLLATE utf8mb4_general_ci DEFAULT 'draft',
  `image_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`post_id`, `title`, `body_text`, `summary`, `author_id`, `creation_date`, `update_date`, `publish_status`, `image_path`) VALUES
(33, 'Hacking', '\r\nWhat is Hacking? Definition, Types, Identification, Safety\r\nLast Updated : 03 Sep, 2021\r\n\r\nAn effort to attack a computer system or a private network inside a computer is known as hacking. Simply, it is unauthorized access to or control of computer network security systems with the intention of committing a crime. Hacking is the process of finding some security holes in a computer system or network in order to gain access to personal or corporate information. One example of computer hacking is the use of a password cracking technique to gain access to a computer system. The process of gaining illegal access to a computer system, or a group of computer systems, is known as hacking. This is accomplished by cracking the passwords and codes that grant access to systems. Cracking is the term used to describe the process of obtaining a password or code. The hacker is the individual who performs the hacking. Following are some of the things that can be hacked:\r\n\r\n    Single systems\r\n    Email account\r\n    A group of systems\r\n    LAN network\r\n    A website\r\n    Social media sites, etc.\r\n\r\nHackers \r\n\r\nComputer hackers are unauthorized users who gain access to computers in order to steal, alter, or delete data, generally by installing malicious software without your knowledge or agreement. They can get access to the information you don’t want them to have thanks to their cunning techniques and in-depth technological knowledge. Any device is connected to the Internet is at risk from computer hackers and online predators. To distribute hazardous malware to your computer and damage your network security, these online criminals generally use spam messages, phishing emails or instant messages, and websites.\r\n\r\nTypes of Hackers:\r\n\r\nTo elaborate on the aforementioned hacking aims, it is vital to understand the various sorts of hackers that exist in the cyber segment in order to distinguish between their responsibilities and objectives. The types of hackers are:\r\n\r\n    Black Hat Hackers: These types of hackers, often known as crackers and always have a malicious motive and gain illegal access to computer networks and websites. Their goal is to make money by stealing secret organizational data, stealing funds from online bank accounts, violating privacy rights to benefit criminal organizations, and so on. In today’s world, the majority of hackers fall into this category and conduct their business in a murky manner. Black hat hackers are nefarious individuals who aim to utilize their technical expertise to exploit and harm others. They usually have the expertise and training to get into computer networks without the consent of the owners, attack security holes, and circumvent security procedures. With the malevolent goal of gaining unauthorized access to networks and systems, they attack to steal data, spread malware causing damage to systems.\r\n    White Hat Hackers/Ethical Hackers: White hat hackers (sometimes referred to as ethical hackers) are the polar opposites of black hat hackers. They employ their technical expertise to defend the planet against malicious hackers. White hats are employed by businesses and government agencies as data security analysts, researchers, security specialists, etc. White hat hackers, with the permission of the system owner and with good motives, use the same hacking tactics that the black hackers use. They can work as contractors, freelancers, or in-house for the companies. They assist their customers in resolving security flaws before they are exploited by criminal hackers.\r\n    Gray Hat Hackers: They fall somewhere between the above-mentioned types of hackers, in that they gain illegal access to a system but do so without any malicious intent. The goal is to expose the system’s weaknesses. Instead of exploiting vulnerabilities for unlawful gains, grey hat hackers may offer to repair vulnerabilities they’ve identified through their own unauthorized actions. Example: They may, for example, infiltrate your website, application without your permission to seek vulnerabilities. They rarely, if ever, try to harm others. Grey hats do this to obtain notoriety and reputation in the cyber security industry, which helps them further their careers as security experts in the long run. This move, on the other hand, harms the reputation of the organizations whose security flaws or exploits are made public.\r\n    Red Hat Hackers: Also known as eagle-eyed hackers. Red hat hackers want to stop threat actors from launching unethical assaults. The red hat hackers aim the same as ethical hackers, but their methods differ, the red hat hackers may utilize illegal or extreme methods. Red hat hackers frequently use cyber attacks against threat actors’ systems.\r\n    Blue Hat Hackers: Safety experts that work outside of the organization are known as blue hat hackers. Before releasing new software, companies frequently encourage them to test it and uncover security flaws. Companies occasionally hold meetings for blue hat hackers to help them uncover flaws in their critical internet systems. Money and fame aren’t necessarily important to some hackers. They hack to exact personal vengeance on a person, employer, organization, or government for a genuine — or perceived — deception. To hurt their adversaries’ data, websites, or devices, blue hat hackers utilize malicious software and various cyber threats on their rivals’ devices.\r\n    Green Hat Hackers: Green hat hackers aren’t familiar with safety measures or the internal dynamics of the internet, but they’re quick learners who are driven (if not desperate) to advance in the hacking world. Although it is unlikely that they want to damage others, they may do so while “experimenting” with various viruses and attack strategies. As a result, green hat hackers can be dangerous since they are frequently unaware of the implications of their activities – or, even worse, how to correct them.\r\n\r\n', 'Hacking is the unauthorized access and manipulation of a computer system, network, website, or mobile device. Hackers use a variety of techniques to gain access, including phishing emails, malware, and social engineering. The goal of hacking is often to steal sensitive data or cause damage to the system.', 5, '2024-11-23 10:14:43', '2024-11-23 14:44:43', 'published', 'uploads/post_6741ea5be63c95.86242644.jpg'),
(34, 'A Bird', 'bird, (class Aves), any of the more than 10,400 living species unique in having feathers, the major characteristic that distinguishes them from all other animals. A more-elaborate definition would note that they are warm-blooded vertebrates more related to reptiles than to mammals and that they have a four-chambered heart (as do mammals), forelimbs modified into wings (a trait shared with bats), a hard-shelled egg, and keen vision, the major sense they rely on for information about the environment. Their sense of smell is not highly developed, and auditory range is limited. Most birds are diurnal in habit. More than 1,000 extinct species have been identified from fossil remains.\r\n\r\nSince earliest times birds have been not only a material but also a cultural resource. Bird figures were created by prehistoric humans in the Lascaux Grotto of France and have featured prominently in the mythology and literature of societies throughout the world. Long before ornithology was practiced as a science, interest in birds and the knowledge of them found expression in conversation and stories, which then crystallized into the records of general culture. Ancient Egyptian hieroglyphs and paintings, for example, include bird figures. The Bible refers to Noah’s use of the raven and dove to bring him information about the proverbial Flood.\r\n\r\nVarious bird attributes, real or imagined, have led to their symbolic use in language as in art. Aesop’s fables abound in bird characters. The Physiologus and its descendants, the bestiaries of the Middle Ages, contain moralistic writings that use birds as symbols for conveying ideas but indicate little knowledge of the birds themselves. Supernatural beliefs about birds probably took hold as early as recognition of the fact that some birds were good to eat. Australian Aborigines, for example, drove the black-and-white flycatcher from camp, lest it overhear conversation and carry the tales to enemies. Peoples of the Pacific Islands saw frigate birds as symbols of the Sun and as carriers of omens and frequently portrayed them in their art. The raven—a common symbol of dark prophecy—was the most important creature to the Indians of the Pacific Northwest and was immortalized in Edgar Allan Poe’s poem “The Raven.” Eagles have long been symbols of power and prestige in many parts of the world, including Europe, where their representations are often seen in heraldry. Native Americans sprinkled eagle down before guests as a sign of peace and friendship, and eagle feathers were commonly used in rituals and headdresses. The resplendent quetzal—the national bird of Guatemala, which shares its name with the currency and is a popular motif in art, fabric, and jewelry—was worshipped and deified by the ancient Mayans and Aztecs. Highly symbolic birds include the phoenix, representing resurrection, and the owl, a common symbol of wisdom but also a reminder of death in Native American mythology. The bird in general has long been a common Christian symbol of the transcendent soul, and in medieval iconography a bird entangled in foliage symbolized the soul embroiled in the materialism of the secular world.', 'Birds are a group of warm-blooded vertebrates constituting the class Aves (Latin: [ˈaveːs]), characterised by feathers, toothless beaked jaws, the laying of hard-shelled eggs, a high metabolic rate, a four-chambered heart, and a strong yet lightweight skeleton.', 1, '2024-11-23 10:25:52', '2024-11-23 14:55:52', 'published', 'uploads/post_6741ecf803d7c7.85421296.jpg'),
(35, 'Digital Art', 'The first use of the term digital art was in the early 1980s when computer engineers devised a paint program which was used by the pioneering digital artist Harold Cohen. This became known as AARON, a robotic machine designed to make large drawings on sheets of paper placed on the floor. Since this early foray into artificial intelligence, Cohen continued to fine-tune the AARON program as technology becomes more sophisticated.\r\n\r\nDigital art can be computer generated, scanned or drawn using a tablet and a mouse. In the 1990s, thanks to improvements in digital technology, it was possible to download video onto computers, allowing artists to manipulate the images they had filmed with a video camera. This gave artists a creative freedom never experienced before with film, allowing them to cut and paste within moving images to create visual collages.\r\n\r\nIn recent times some digital art has become interactive, allowing the audience a certain amount of control over the final image.Subcategories for the art include digital painting, where artists use software to emulate techniques using in physical painting, digital illustration, which involves creating rendered images for other media, and 3D modeling, where artists craft three-dimensional objects and scenes. Pieces of digital art range from captured in unique displays and restricted from duplication to popular memes available for reproduction in commercial products.\r\n\r\nRepositories for digital art include pieces stored on physical media, galleries on display on websites, and collections for download for free or purchase. ', 'Digital art refers to any artistic work or practice that uses digital technology as part of the creative or presentation process. It can also refer to computational art that uses and engages with digital media.', 1, '2024-11-23 13:20:45', '2024-11-23 17:50:45', 'published', 'uploads/post_674215f5bf88f5.49018497.jpg'),
(36, 'Artificial intelligence (AI)', 'Artificial intelligence (AI), in its broadest sense, is intelligence exhibited by machines, particularly computer systems. It is a field of research in computer science that develops and studies methods and software that enable machines to perceive their environment and use learning and intelligence to take actions that maximize their chances of achieving defined goals.[1] Such machines may be called AIs.\r\n\r\nSome high-profile applications of AI include advanced web search engines (e.g., Google Search); recommendation systems (used by YouTube, Amazon, and Netflix); interacting via human speech (e.g., Google Assistant, Siri, and Alexa); autonomous vehicles (e.g., Waymo); generative and creative tools (e.g., ChatGPT, and AI art); and superhuman play and analysis in strategy games (e.g., chess and Go). However, many AI applications are not perceived as AI: \"A lot of cutting edge AI has filtered into general applications, often without being called AI because once something becomes useful enough and common enough it\'s not labeled AI anymore.\"[2][3]\r\n\r\nThe various subfields of AI research are centered around particular goals and the use of particular tools. The traditional goals of AI research include reasoning, knowledge representation, planning, learning, natural language processing, perception, and support for robotics.[a] General intelligence—the ability to complete any task performable by a human on an at least equal level—is among the field\'s long-term goals.[4] To reach these goals, AI researchers have adapted and integrated a wide range of techniques, including search and mathematical optimization, formal logic, artificial neural networks, and methods based on statistics, operations research, and economics.[b] AI also draws upon psychology, linguistics, philosophy, neuroscience, and other fields.[5]\r\n\r\nArtificial intelligence was founded as an academic discipline in 1956,[6] and the field went through multiple cycles of optimism,[7][8] followed by periods of disappointment and loss of funding, known as AI winter.[9][10] Funding and interest vastly increased after 2012 when deep learning outperformed previous AI techniques.[11] This growth accelerated further after 2017 with the transformer architecture,[12] and by the early 2020s hundreds of billions of dollars were being invested in AI (known as the \"AI boom\"). The widespread use of AI in the 21st century exposed several unintended consequences and harms in the present and raised concerns about its risks and long-term effects in the future, prompting discussions about regulatory policies to ensure the safety and benefits of the technology. ', 'Artificial intelligence (AI), in its broadest sense, is intelligence exhibited by machines, particularly computer systems.', 5, '2024-11-23 13:30:32', '2024-11-23 18:00:32', 'published', 'uploads/post_67421840929636.56666252.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `post_categories`
--

CREATE TABLE `post_categories` (
  `post_id` int NOT NULL,
  `category_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post_categories`
--

INSERT INTO `post_categories` (`post_id`, `category_id`) VALUES
(33, 17),
(36, 17),
(34, 18),
(35, 19);

-- --------------------------------------------------------

--
-- Table structure for table `post_tags`
--

CREATE TABLE `post_tags` (
  `post_id` int NOT NULL,
  `tag_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post_tags`
--

INSERT INTO `post_tags` (`post_id`, `tag_id`) VALUES
(33, 37),
(33, 38),
(33, 39),
(34, 40),
(34, 41),
(34, 42),
(34, 43),
(35, 44),
(35, 45),
(35, 46),
(35, 47),
(36, 48),
(36, 49),
(36, 50),
(36, 51);

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `tag_id` int NOT NULL,
  `tag_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`tag_id`, `tag_name`) VALUES
(48, 'AI'),
(42, 'Animal'),
(45, 'Art'),
(49, 'Artificial_Intelligence'),
(41, 'Bird'),
(40, 'Birds'),
(43, 'Creature'),
(46, 'Digital'),
(44, 'Digital_Art'),
(37, 'Hack'),
(39, 'Hacks'),
(51, 'Machine_Learning'),
(50, 'ML'),
(38, 'Programming'),
(47, 'Style');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','editor','author','reader') COLLATE utf8mb4_general_ci NOT NULL,
  `account_status` enum('active','inactive','suspended') COLLATE utf8mb4_general_ci DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password_hash`, `role`, `account_status`) VALUES
(1, 'Dev', 'iamtatva@gmail.com', '$2y$10$Kl/ykeSH3qkOS812.SZf5Ok8aVjIwQi8Wr/5y31KoN/3X9s0oURwW', 'author', 'active'),
(4, 'admin', 'admin@gmail.com', '$2y$10$isiFxf4KpmLHPweP.go9gu6oyX21cVdSIcl0DjF.XtqaDBDeM8FRK', 'admin', 'active'),
(5, 'Ritik', 'ritik@gmail.com', '$2y$10$bERRx6F2g5JZC8Dxs0D/Ue7Qi27SR2mxaYSUM5weTnxCbbbilW9lm', 'author', 'active'),
(6, 'Rajiv', 'rajiv@gmail.com', '$2y$10$KQ/DYWvwG.Tx7D.eisxqW.y/mJ8ioWAgyEU23WkkvPI9QPyoWRGHa', 'reader', 'active'),
(7, 'Aditya', 'aditya@gmail.com', '$2y$10$6k/K5yUavJbVHCCe4Ron2.CZfoU.QJ4KYnL7HLd0zWTpycX1U7Wma', 'author', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `post_categories`
--
ALTER TABLE `post_categories`
  ADD PRIMARY KEY (`post_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `post_tags`
--
ALTER TABLE `post_tags`
  ADD PRIMARY KEY (`post_id`,`tag_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`tag_id`),
  ADD UNIQUE KEY `tag_name` (`tag_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `tag_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `post_categories`
--
ALTER TABLE `post_categories`
  ADD CONSTRAINT `post_categories_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE;

--
-- Constraints for table `post_tags`
--
ALTER TABLE `post_tags`
  ADD CONSTRAINT `post_tags_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`tag_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
