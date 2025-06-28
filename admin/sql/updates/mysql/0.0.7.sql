DROP TABLE IF EXISTS `#__blaulichtmonitor_greetings`;

CREATE TABLE `#__blaulichtmonitor_greetings` (
    `id` SERIAL NOT NULL,
    `greeting` VARCHAR(200) NOT NULL,
    `published` BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;

INSERT INTO `#__blaulichtmonitor_greetings` (`greeting`) VALUES
    ('Hello World!'),
    ('Good bye World!');