CREATE DATABASE biblioteca;
USE biblioteca;

/* ===================== TABELAS ===================== */

CREATE TABLE Usuario (
    id_usuario INT PRIMARY KEY,
    contacto VARCHAR(20),
    tipo_usuario VARCHAR(50),
    data_usuario DATE,
    nome VARCHAR(50),
    senha VARCHAR(90)
);

CREATE TABLE Livro (
    id_livro INT PRIMARY KEY,
    titulo VARCHAR(100),
    autor VARCHAR(100),
    quantidade INT,
    editora VARCHAR(100),
    imagem VARCHAR(255),
    edicao VARCHAR(50),
    data_livro DATE
);

CREATE TABLE Leitor (
    id INT PRIMARY KEY,
    nome VARCHAR(100),
    email VARCHAR(100),
    nif VARCHAR(50),
    numero_telefone VARCHAR(15),
    data_leitor DATE
);

CREATE TABLE Multa (
    id_multa INT PRIMARY KEY,
    valor VARCHAR(50),
    data_multa DATE
);

CREATE TABLE Reserva (
    id_reserva INT PRIMARY KEY,
    data_reserva DATE,
    nome_leitor VARCHAR(100),
    prazo_reserva DATE,
    fk_Usuario_id_usuario INT,
    fk_Livro_id_livro INT,

    FOREIGN KEY (fk_Usuario_id_usuario)
        REFERENCES Usuario(id_usuario)
        ON DELETE CASCADE,

    FOREIGN KEY (fk_Livro_id_livro)
        REFERENCES Livro(id_livro)
        ON DELETE CASCADE
);

CREATE TABLE Emprestimo (
    id_emprestimo INT PRIMARY KEY,
    data_emprestimo DATE,
    data_prevista DATE,
    status VARCHAR(20),
    fk_Usuario_id_usuario INT,
    fk_Livro_id_livro INT,

    FOREIGN KEY (fk_Usuario_id_usuario)
        REFERENCES Usuario(id_usuario)
        ON DELETE RESTRICT,

    FOREIGN KEY (fk_Livro_id_livro)
        REFERENCES Livro(id_livro)
        ON DELETE RESTRICT
);

CREATE TABLE Devolucao (
    id_devolucao INT PRIMARY KEY,
    data_devolucao DATE,
    fk_Multa_id_multa INT NULL,

    FOREIGN KEY (fk_Multa_id_multa)
        REFERENCES Multa(id_multa)
        ON DELETE SET NULL
);

/* ===================== TABELAS DE RELAÇÃO ===================== */

CREATE TABLE emprestar (
    fk_Emprestimo_id_emprestimo INT,
    fk_Leitor_id INT,

    PRIMARY KEY (fk_Emprestimo_id_emprestimo, fk_Leitor_id),

    FOREIGN KEY (fk_Emprestimo_id_emprestimo)
        REFERENCES Emprestimo(id_emprestimo)
        ON DELETE RESTRICT,

    FOREIGN KEY (fk_Leitor_id)
        REFERENCES Leitor(id)
        ON DELETE RESTRICT
);

CREATE TABLE devolver (
    fk_Devolucao_id_devolucao INT,
    fk_Emprestimo_id_emprestimo INT,

    PRIMARY KEY (fk_Devolucao_id_devolucao, fk_Emprestimo_id_emprestimo),

    FOREIGN KEY (fk_Devolucao_id_devolucao)
        REFERENCES Devolucao(id_devolucao)
        ON DELETE RESTRICT,

    FOREIGN KEY (fk_Emprestimo_id_emprestimo)
        REFERENCES Emprestimo(id_emprestimo)
        ON DELETE RESTRICT
);