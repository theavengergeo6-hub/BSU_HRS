-- FAQ section (accordion on index.php). Run on your app database (e.g. bsu_hrs_schema).

CREATE TABLE IF NOT EXISTS `faq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(500) NOT NULL,
  `answer` text NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample rows (adjust text for your site)
INSERT INTO `faq` (`question`, `answer`, `sort_order`) VALUES
('Who may request to use the Livelihood Training Center facility?', 'The Livelihood Training Center may be requested for use by BatStateU units, extension project teams, partner agencies, LGUs, NGOs, and other approved organizations, subject to availability and compliance with university policies.', 1),
('Are Extension Services activities prioritized in the schedule?', 'Yes. Extension Services activities and university-sanctioned programs are given priority in scheduling, subject to advance booking and approval.', 2),
('How can we submit a request or make inquiries?', 'You may submit a request or inquiry through the Reservation page, by contacting the office directly, or via the contact details provided on this website.', 3),
('What general rules must users observe during facility use?', 'Users must comply with university policies, maintain cleanliness, and use the facility only for the approved purpose and within the reserved time slot.', 4);
