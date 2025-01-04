-- Insert data into the authors table
INSERT INTO authors (name, biography, birth_date) VALUES
('J.K. Rowling', 'British author, best known for the Harry Potter series.', '1965-07-31'),
('George R.R. Martin', 'American novelist and short story writer, best known for A Song of Ice and Fire.', '1948-09-20'),
('Agatha Christie', 'English writer, known for her detective novels.', '1890-09-15'),
('Stephen King', 'American author of horror, supernatural fiction, suspense, and fantasy novels.', '1947-09-21');

-- Insert data into the categories table
INSERT INTO categories (name, description) VALUES
('Fantasy', 'Books with magical or supernatural elements.'),
('Mystery', 'Books focused on solving a crime or uncovering secrets.'),
('Science Fiction', 'Books exploring futuristic and scientific concepts.'),
('Horror', 'Books designed to scare or thrill readers.');

-- Insert data into the books table
INSERT INTO books (title, author_id, category_id, description, price, stock, cover_image) VALUES
('Harry Potter and the Sorcerer\'s Stone', 1, 1, 'A young wizard discovers his magical heritage and begins his adventures.', 20.99, 100, 'harry_potter1.jpg'),
('A Game of Thrones', 2, 1, 'The first book in the A Song of Ice and Fire series, set in the fictional land of Westeros.', 15.99, 50, 'game_of_thrones.jpg'),
('Murder on the Orient Express', 3, 2, 'Detective Hercule Poirot solves a murder aboard a luxury train.', 10.99, 75, 'murder_on_orient_express.jpg'),
('The Shining', 4, 4, 'A family experiences supernatural occurrences at an isolated hotel.', 14.99, 60, 'the_shining.jpg');

-- Insert data into the orders table
INSERT INTO orders (user_id, total_price, order_date, status) VALUES
(1, 31.98, '2024-12-01 10:30:00', 'Completed'),
(2, 20.99, '2024-12-02 15:00:00', 'Processing'),
(3, 14.99, '2024-12-03 11:45:00', 'Pending');

-- Insert data into the order_details table
INSERT INTO order_details (order_id, book_id, quantity, price) VALUES
(1, 1, 1, 20.99),
(1, 3, 1, 10.99),
(2, 1, 1, 20.99),
(3, 4, 1, 14.99);

-- Insert data into the reviews table
INSERT INTO reviews (book_id, user_id, rating, review_text) VALUES
(1, 1, 5, 'An amazing book! A must-read for all fantasy lovers.'),
(3, 2, 4, 'Great mystery novel with an unexpected twist.'),
(4, 3, 3, 'Quite thrilling but the ending felt rushed.');

-- Insert data into the wishlist table
INSERT INTO wishlist (user_id, book_id, added_at) VALUES
(1, 2, '2024-12-01 08:00:00'),
(2, 4, '2024-12-02 09:00:00'),
(3, 1, '2024-12-03 12:00:00');
