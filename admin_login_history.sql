CREATE TABLE admin_login_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT,
    ip_address VARCHAR(50),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);