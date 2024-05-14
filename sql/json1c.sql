CREATE TABLE IF NOT EXISTS `json1c` (
            `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
            `client_id` INT NOT NULL,
            `fluid_tag` VARCHAR(255) NOT NULL,
            `client_mail` VARCHAR(255) NOT NULL,
            `client_mail_id` VARCHAR(255) NOT NULL,
            `client_code` VARCHAR(255) NOT NULL,
            `invoice_id` VARCHAR(255) NOT NULL,
            `invoice_status` INT NOT NULL,
            `invoice_number` INT NOT NULL,
            `invoice_date` TIMESTAMP NOT NULL,
            `invoice_price` DECIMAL NOT NULL,

            FOREIGN KEY (client_id) REFERENCES visitors_info (id)
)