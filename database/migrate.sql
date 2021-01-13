CREATE TABLE users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL, # for bcrypt 60 would be enough, but default php algorithm can change in future
    avatar_path VARCHAR(255),
    # Counting every time amount of messages is heavy task
    # Also, it allows counting of messages in deleted threads
    messages_count INT UNSIGNED NOT NULL DEFAULT 0,
    remember_token CHAR(23),
    remember_token_expires_at TIMESTAMP,
    role VARCHAR(20) NOT NULL,
    is_banned BOOLEAN DEFAULT FALSE,

    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW(),
    deleted_at TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY (username),
    UNIQUE KEY (email),
    UNIQUE KEY (remember_token)
);

CREATE TABLE categories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,

    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW(),
    deleted_at TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE (name)
);

CREATE TABLE boards (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    category_id BIGINT UNSIGNED,
    author_id BIGINT UNSIGNED,

    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW(),
    deleted_at TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY (slug),
    FOREIGN KEY (category_id)
        REFERENCES categories(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,
    FOREIGN KEY (author_id)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
);

CREATE TABLE threads (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    status VARCHAR(20) NOT NULL,
    is_pinned BOOLEAN DEFAULT FALSE,
    board_id BIGINT UNSIGNED,
    author_id BIGINT UNSIGNED,

    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW(),
    deleted_at TIMESTAMP,

    PRIMARY KEY (id),
    FOREIGN KEY (author_id)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,
    FOREIGN KEY (board_id)
        REFERENCES boards(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
);

CREATE TABLE messages (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    content TEXT NOT NULL,
    author_id BIGINT UNSIGNED,
    thread_id BIGINT UNSIGNED NOT NULL,

    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW(),
    deleted_at TIMESTAMP,

    PRIMARY KEY (id),
    FOREIGN KEY (author_id)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,
    FOREIGN KEY (thread_id)
        REFERENCES threads(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);
