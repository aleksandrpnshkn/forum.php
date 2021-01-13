INSERT INTO users (username, email, password, role)
VALUES
    ('milk', 'milk@example.tld', 'Xe[.B,3@T5[_cw~J', 'user'),
    ('omeganebula', 'omeganebula@example.tld', 'z&5R!j:2}eDRQgkc', 'user'),
    ('wheatbread', 'wheatbread@example.tld', ',yz_<un<5gb6WLS8', 'user'),
    ('dorothy', 'dorothy@example.tld', 'Q^s4+ZA:<5>Q.,W^', 'user'),
    ('saxophone', 'saxophone@example.tld', 'EUu47~;VbsmDUe?7', 'user'),
    ('tabletennis', 'tabletennis@example.tld', '&cf()PR6C;>d>Lf', 'user'),
    ('rabbit', 'rabbit@example.tld', '5(eLR7Q8pZ<pL:Tk', 'moderator');

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
    ('If my calculator had a history', 'Open', true, 1, 1),
    ('I love eating toasted cheese and tuna sandwiches', 'Open', true, 1, 2),
    ('We need to rent a room for our party', 'Open', false, 1, 2),
    ('his seven-layer cake only had six layers', 'Open', false, 2, 1),
    ('If my calculator had a history', 'Open', true, 1, 1),
    ('I love eating toasted cheese and tuna sandwiches', 'Open', true, 1, 2),
    ('We need to rent a room for our party', 'Open', false, 1, 2),
    ('his seven-layer cake only had six layers', 'Open', false, 2, 1),
    ('If my calculator had a history', 'Open', true, 1, 1),
    ('I love eating toasted cheese and tuna sandwiches', 'Open', true, 1, 2),
    ('We need to rent a room for our party', 'Open', false, 1, 2),
    ('his seven-layer cake only had six layers', 'Open', false, 2, 1),
    ('If my calculator had a history', 'Open', true, 1, 1),
    ('I love eating toasted cheese and tuna sandwiches', 'Open', true, 1, 2),
    ('We need to rent a room for our party', 'Open', false, 1, 2),
    ('his seven-layer cake only had six layers', 'Open', false, 2, 1),
    ('If you like tuna and tomato sauce- try combining the two', 'Open', false, 2, 1),
    ('If I don’t like something, I’ll stay away from it.', 'Open', false, 3, 1),
    ('She had convinced her kids that any mushroom found on the ground would kill them if they touched it.', 'Open', false, 4, 1),
    ('The beach was crowded with snow leopards.', 'Open', false, 5, 1),
    ('Nudist colonies shun fig-leaf couture', 'Open', false, 6, 1),
    ('He embraced his new life as an eggplant', 'Open', false, 6, 1);
