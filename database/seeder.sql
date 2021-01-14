INSERT INTO users (username, email, password, role, is_banned)
VALUES
    ('milk', 'milk@example.tld', 'Xe[.B,3@T5[_cw~J', 'user', false),
    ('omeganebula', 'omeganebula@example.tld', 'z&5R!j:2}eDRQgkc', 'user', false),
    ('wheatbread', 'wheatbread@example.tld', ',yz_<un<5gb6WLS8', 'user', false),
    ('dorothy', 'dorothy@example.tld', 'Q^s4+ZA:<5>Q.,W^', 'user', true),
    ('saxophone', 'saxophone@example.tld', 'EUu47~;VbsmDUe?7', 'user', false),
    ('tabletennis', 'tabletennis@example.tld', '&cf()PR6C;>d>Lf', 'user', false),
    ('rabbit', 'rabbit@example.tld', '5(eLR7Q8pZ<pL:Tk', 'moderator', false);

INSERT INTO categories (name)
VALUES ('Vegetables'), ('Coins'), ('Loud noises');

INSERT INTO boards (name, slug, description, category_id, author_id)
VALUES
    ('Quinible', 'quinible', 'He dreamed of eating green apples with worms.', 1, 1),
    ('Discountenance', 'discountenance', NULL, 1, 2),
    ('Vocative', 'vocative', NULL, 1, 2),
    ('Zufolo', 'zufolo', 'small flute used to train songbirds', 2, 1),
    ('Theocentrism', 'theocentrism', NULL, 2, 1),
    ('Bibliopoesy', 'bibliopoesy', 'The hummingbird\'s wings blurred while it eagerly sipped the sugar water from the feeder.', 3, 1);

INSERT INTO threads (name, status, is_pinned, board_id, author_id)
VALUES
    ('If my calculator had a history', 'Open', false, 1, 1),
    ('We need to rent a room for our party', 'Open', false, 1, 2),
    ('his seven-layer cake only had six layers', 'Open', false, 2, 1),
    ('If my calculator had a history', 'Open', true, 1, 1),
    ('I love eating toasted cheese and tuna sandwiches', 'Open', false, 1, 2),
    ('We need to rent a room for our party', 'Open', false, 1, 2),
    ('his seven-layer cake only had six layers', 'Open', false, 2, 1),
    ('If my calculator had a history', 'Open', false, 1, 1),
    ('I love eating toasted cheese and tuna sandwiches', 'Open', false, 1, 2),
    ('We need to rent a room for our party', 'Open', false, 1, 2),
    ('his seven-layer cake only had six layers', 'Open', false, 2, 1),
    ('If my calculator had a history', 'Open', false, 1, 1),
    ('I love eating toasted cheese and tuna sandwiches', 'Open', true, 1, 2),
    ('We need to rent a room for our party', 'Open', false, 1, 2),
    ('his seven-layer cake only had six layers', 'Open', false, 2, 1),
    ('If you like tuna and tomato sauce- try combining the two', 'Open', false, 2, 1),
    ('If I don’t like something, I’ll stay away from it.', 'Open', false, 3, 1),
    ('She had convinced her kids that any mushroom found on the ground would kill them if they touched it.', 'Open', false, 4, 1),
    ('The beach was crowded with snow leopards.', 'Open', false, 5, 1),
    ('Nudist colonies shun fig-leaf couture', 'Open', false, 6, 1),
    ('He embraced his new life as an eggplant', 'Open', false, 6, 1),
    ('Where are all messages? :O', 'Open', true, 1, 1);

INSERT INTO messages (content, author_id, thread_id)
VALUES
    ('The stranger officiates the meal.', 1, 22),
    ('lol', 2, 22),
    ('The newly planted trees were held up by wooden frames in hopes they could survive the next storm. ', 3, 22),
    (':DDD', 4, 22),
    ('At that moment she realized she had a sixth sense.', 5, 22),
    ('She was amazed by the large chunks of ice washing up on the beach.', 6, 22),
    ('He had unknowingly taken up sleepwalking as a nighttime hobby.', 7, 22),
    ('Mothers spend months of their lives waiting on their children.', 1, 22),
    ('The snow-covered path was no help in finding his way out of the back-country.', 2, 22),
    ('I think I will buy the red car, or I will lease the blue one.', 3, 22),
    ('BAN!', 4, 22),
    ('Hit me with your pet shark!', 5, 22),
    ('spam bla bla bla', 6, 22),
    ('spam bla bla bla', 6, 22),
    ('spam bla bla bla', 6, 22),
    ('Carol drank the blood as if she were a vampire.', 7, 22),

    ('Hello', 7, 1),
    ('Hello', 7, 2),
    ('Hello', 7, 3),
    ('Hello', 7, 4),
    ('Hello', 7, 5),
    ('Hello', 7, 6),
    ('Hello', 7, 7),
    ('Hello', 7, 8),
    ('Hello', 7, 9),
    ('Hello', 7, 10),
    ('Hello', 7, 11),
    ('Hello', 7, 12),
    ('Hello', 7, 13),
    ('Hello', 7, 14),
    ('Hello', 7, 15),
    ('Hello', 7, 16),
    ('Hello', 7, 17),
    ('Hello', 7, 18),
    ('Hello', 7, 19),
    ('Hello', 7, 20),
    ('Hello', 7, 21);
