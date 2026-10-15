-- ============================================================
-- ANGOLA PREÇO JUSTO
-- Base de Dados v2 — versão completa para o projecto final
-- Compatível com MySQL 8.x / MariaDB usado em XAMPP
--
-- PRINCIPAIS FUNCIONALIDADES:
-- 1. Avaliação da qualidade dos produtos
-- 2. Preferências/localização do consumidor
-- 3. Histórico de preços ligado à oferta Loja + Produto
-- 4. Procedures para garantir que só lojas autorizadas alteram preços
-- 5. Data da última actualização do preço
-- 6. Variações do mesmo produto (marca + quantidade + unidade)
-- ============================================================


-- ============================================================
-- CRIAÇÃO DA BASE DE DADOS
-- ============================================================

CREATE DATABASE IF NOT EXISTS precos_angola
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE precos_angola;

SET NAMES utf8mb4;


-- ============================================================
-- 1. UTILIZADORES
-- ============================================================

CREATE TABLE IF NOT EXISTS utilizadores (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    nome VARCHAR(100) NOT NULL,

    email VARCHAR(150) NOT NULL UNIQUE,

    telefone VARCHAR(30) NULL,

    senha_hash VARCHAR(255) NOT NULL,

    perfil ENUM(
        'cliente',
        'loja',
        'administrador'
    ) NOT NULL DEFAULT 'cliente',

    estado ENUM(
        'ativo',
        'inativo',
        'bloqueado'
    ) NOT NULL DEFAULT 'ativo',

    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    INDEX indice_utilizadores_perfil (perfil),

    INDEX indice_utilizadores_estado (estado)

) ENGINE=InnoDB;


-- ============================================================
-- 2. PREFERÊNCIAS / LOCALIZAÇÃO DO CLIENTE
--
-- A localização é opcional.
-- Pode ser escolhida manualmente ou obtida pelo navegador,
-- conforme o consentimento do utilizador.
-- ============================================================

CREATE TABLE IF NOT EXISTS preferencias_utilizadores (

    utilizador_id INT UNSIGNED PRIMARY KEY,

    provincia VARCHAR(100) NULL,

    municipio VARCHAR(100) NULL,

    bairro VARCHAR(100) NULL,

    latitude DECIMAL(10, 8) NULL,

    longitude DECIMAL(11, 8) NULL,

    modo_localizacao ENUM(
        'manual',
        'gps'
    ) NOT NULL DEFAULT 'manual',

    raio_pesquisa_km DECIMAL(6, 2) NOT NULL DEFAULT 10.00,

    permitir_localizacao BOOLEAN NOT NULL DEFAULT FALSE,

    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_preferencias_utilizador
        FOREIGN KEY (utilizador_id)
        REFERENCES utilizadores(id)
        ON DELETE CASCADE,

    CONSTRAINT chk_raio_pesquisa
        CHECK (raio_pesquisa_km > 0)

) ENGINE=InnoDB;


-- ============================================================
-- 3. LOJAS
--
-- Cada loja pertence a um utilizador com perfil "loja".
-- ============================================================

CREATE TABLE IF NOT EXISTS lojas (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    utilizador_id INT UNSIGNED NOT NULL,

    nome VARCHAR(150) NOT NULL,

    slug VARCHAR(160) NOT NULL UNIQUE,

    descricao TEXT NULL,

    telefone VARCHAR(30) NULL,

    endereco VARCHAR(255) NOT NULL,

    bairro VARCHAR(100) NOT NULL,

    municipio VARCHAR(100) NOT NULL,

    provincia VARCHAR(100) NOT NULL,

    latitude DECIMAL(10, 8) NULL,

    longitude DECIMAL(11, 8) NULL,

    estado ENUM(
        'pendente',
        'aprovada',
        'rejeitada',
        'suspensa'
    ) NOT NULL DEFAULT 'pendente',

    caminho_logotipo VARCHAR(255) NULL,

    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_loja_utilizador
        FOREIGN KEY (utilizador_id)
        REFERENCES utilizadores(id)
        ON DELETE RESTRICT,

    INDEX indice_lojas_localizacao (
        provincia,
        municipio,
        bairro
    ),

    INDEX indice_lojas_estado (estado),

    INDEX indice_lojas_utilizador (utilizador_id)

) ENGINE=InnoDB;


-- ============================================================
-- 4. CATEGORIAS DE PRODUTOS
-- ============================================================

CREATE TABLE IF NOT EXISTS categorias (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    nome VARCHAR(100) NOT NULL UNIQUE,

    slug VARCHAR(110) NOT NULL UNIQUE,

    icone VARCHAR(50) NULL,

    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP

) ENGINE=InnoDB;


-- ============================================================
-- 5. PRODUTOS BASE
--
-- Exemplos:
-- "Arroz"
-- "Óleo"
-- "Açúcar"
-- ============================================================

CREATE TABLE IF NOT EXISTS produtos (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    categoria_id INT UNSIGNED NOT NULL,

    nome VARCHAR(150) NOT NULL,

    descricao TEXT NULL,

    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_produto_categoria
        FOREIGN KEY (categoria_id)
        REFERENCES categorias(id)
        ON DELETE RESTRICT,

    INDEX indice_produtos_nome (nome),

    INDEX indice_produtos_categoria (categoria_id)

) ENGINE=InnoDB;


-- ============================================================
-- 6. VARIAÇÕES DO PRODUTO
--
-- Evita comparar produtos de pesos/volumes diferentes.
--
-- Exemplos:
--
-- Arroz | Marca X | 25 kg
-- Arroz | Marca Y | 5 kg
-- Arroz | Marca Z | 1 kg
-- ============================================================

CREATE TABLE IF NOT EXISTS variacoes_produtos (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    produto_id INT UNSIGNED NOT NULL,

    marca VARCHAR(100) NOT NULL DEFAULT 'Genérico',

    quantidade DECIMAL(10, 3) NOT NULL,

    unidade_medida ENUM(
        'g',
        'kg',
        'ml',
        'L',
        'unidade'
    ) NOT NULL,

    nome_variacao VARCHAR(150) NULL,

    codigo_barras VARCHAR(50) NULL UNIQUE,

    descricao TEXT NULL,

    caminho_imagem VARCHAR(255) NULL,

    estado ENUM(
        'ativo',
        'inativo'
    ) NOT NULL DEFAULT 'ativo',

    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_variacao_produto
        FOREIGN KEY (produto_id)
        REFERENCES produtos(id)
        ON DELETE RESTRICT,

    CONSTRAINT chk_quantidade_variacao
        CHECK (quantidade > 0),

    INDEX indice_variacoes_produto (produto_id),

    INDEX indice_variacoes_marca (marca),

    UNIQUE KEY uk_definicao_variacao (
        produto_id,
        marca,
        quantidade,
        unidade_medida
    )

) ENGINE=InnoDB;


-- ============================================================
-- 7. OFERTAS DA LOJA
--
-- Liga uma loja a uma variante exacta de produto.
-- Guarda o preço actual e a disponibilidade.
-- ============================================================

CREATE TABLE IF NOT EXISTS produtos_lojas (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    loja_id INT UNSIGNED NOT NULL,

    variacao_produto_id INT UNSIGNED NOT NULL,

    preco DECIMAL(12, 2) NOT NULL,

    em_estoque BOOLEAN NOT NULL DEFAULT TRUE,

    estado ENUM(
        'ativo',
        'inativo'
    ) NOT NULL DEFAULT 'ativo',

    ultima_atualizacao_preco DATETIME NULL,

    ultima_atualizacao_estoque DATETIME NULL,

    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_produto_loja_loja
        FOREIGN KEY (loja_id)
        REFERENCES lojas(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_produto_loja_variacao
        FOREIGN KEY (variacao_produto_id)
        REFERENCES variacoes_produtos(id)
        ON DELETE RESTRICT,

    CONSTRAINT chk_preco_produto_loja
        CHECK (preco >= 0),

    UNIQUE KEY uk_loja_variacao (
        loja_id,
        variacao_produto_id
    ),

    INDEX indice_produtos_lojas_preco (preco),

    INDEX indice_produtos_lojas_loja (loja_id),

    INDEX indice_produtos_lojas_variacao (
        variacao_produto_id
    ),

    INDEX indice_produtos_lojas_estoque (
        em_estoque
    ),

    INDEX indice_produtos_lojas_data_preco (
        ultima_atualizacao_preco
    )

) ENGINE=InnoDB;


-- ============================================================
-- 8. HISTÓRICO DE PREÇOS
--
-- Ligado directamente à oferta da loja.
-- ============================================================

CREATE TABLE IF NOT EXISTS historico_precos (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    produto_loja_id INT UNSIGNED NOT NULL,

    preco_anterior DECIMAL(12, 2) NULL,

    novo_preco DECIMAL(12, 2) NOT NULL,

    registado_por_utilizador_id INT UNSIGNED NULL,

    registado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_historico_produto_loja
        FOREIGN KEY (produto_loja_id)
        REFERENCES produtos_lojas(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_historico_utilizador
        FOREIGN KEY (registado_por_utilizador_id)
        REFERENCES utilizadores(id)
        ON DELETE SET NULL,

    CONSTRAINT chk_historico_novo_preco
        CHECK (novo_preco >= 0),

    CONSTRAINT chk_historico_preco_anterior
        CHECK (
            preco_anterior IS NULL
            OR preco_anterior >= 0
        ),

    INDEX indice_historico_oferta_data (
        produto_loja_id,
        registado_em
    ),

    INDEX indice_historico_utilizador (
        registado_por_utilizador_id
    )

) ENGINE=InnoDB;


-- ============================================================
-- 9. AVALIAÇÕES DE QUALIDADE DOS PRODUTOS
--
-- Classificação de 1 a 5 estrelas.
-- ============================================================

CREATE TABLE IF NOT EXISTS avaliacoes_produtos (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    utilizador_id INT UNSIGNED NOT NULL,

    variacao_produto_id INT UNSIGNED NOT NULL,

    classificacao TINYINT UNSIGNED NOT NULL,

    comentario TEXT NULL,

    estado ENUM(
        'pendente',
        'publicada',
        'oculta'
    ) NOT NULL DEFAULT 'publicada',

    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_avaliacao_utilizador
        FOREIGN KEY (utilizador_id)
        REFERENCES utilizadores(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_avaliacao_variacao
        FOREIGN KEY (variacao_produto_id)
        REFERENCES variacoes_produtos(id)
        ON DELETE RESTRICT,

    CONSTRAINT chk_classificacao_avaliacao
        CHECK (classificacao BETWEEN 1 AND 5),

    UNIQUE KEY uk_utilizador_variacao_avaliacao (
        utilizador_id,
        variacao_produto_id
    ),

    INDEX indice_avaliacoes_variacao (
        variacao_produto_id
    ),

    INDEX indice_avaliacoes_classificacao (
        classificacao
    ),

    INDEX indice_avaliacoes_estado (
        estado
    )

) ENGINE=InnoDB;


-- ============================================================
-- 10. FAVORITOS
-- ============================================================

CREATE TABLE IF NOT EXISTS favoritos (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    utilizador_id INT UNSIGNED NOT NULL,

    variacao_produto_id INT UNSIGNED NOT NULL,

    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_favorito_utilizador
        FOREIGN KEY (utilizador_id)
        REFERENCES utilizadores(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_favorito_variacao
        FOREIGN KEY (variacao_produto_id)
        REFERENCES variacoes_produtos(id)
        ON DELETE CASCADE,

    UNIQUE KEY uk_utilizador_variacao_favorito (
        utilizador_id,
        variacao_produto_id
    )

) ENGINE=InnoDB;


-- ============================================================
-- 11. DENÚNCIAS / PROBLEMAS NOS PREÇOS
-- ============================================================

CREATE TABLE IF NOT EXISTS denuncias (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    utilizador_id INT UNSIGNED NOT NULL,

    produto_loja_id INT UNSIGNED NULL,

    motivo VARCHAR(100) NOT NULL,

    descricao TEXT NOT NULL,

    estado ENUM(
        'pendente',
        'em_analise',
        'resolvida',
        'rejeitada'
    ) NOT NULL DEFAULT 'pendente',

    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_denuncia_utilizador
        FOREIGN KEY (utilizador_id)
        REFERENCES utilizadores(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_denuncia_produto_loja
        FOREIGN KEY (produto_loja_id)
        REFERENCES produtos_lojas(id)
        ON DELETE SET NULL,

    INDEX indice_denuncias_estado (
        estado
    ),

    INDEX indice_denuncias_produto_loja (
        produto_loja_id
    )

) ENGINE=InnoDB;


-- ============================================================
-- 12. LOGS DE AUDITORIA
-- ============================================================

CREATE TABLE IF NOT EXISTS logs_atividades (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    utilizador_id INT UNSIGNED NULL,

    acao VARCHAR(150) NOT NULL,

    detalhes TEXT NULL,

    endereco_ip VARCHAR(45) NULL,

    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_log_atividade_utilizador
        FOREIGN KEY (utilizador_id)
        REFERENCES utilizadores(id)
        ON DELETE SET NULL,

    INDEX indice_log_utilizador_data (
        utilizador_id,
        criado_em
    ),

    INDEX indice_log_data (
        criado_em
    )

) ENGINE=InnoDB;


-- ============================================================
-- 13. CONFIGURAÇÕES DA PLATAFORMA
-- ============================================================

CREATE TABLE IF NOT EXISTS configuracoes_plataforma (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    chave_configuracao VARCHAR(100) NOT NULL UNIQUE,

    valor_configuracao TEXT NOT NULL,

    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP

) ENGINE=InnoDB;


-- ============================================================
-- 14. CATEGORIAS INICIAIS
-- ============================================================

INSERT IGNORE INTO categorias (
    nome,
    slug,
    icone
) VALUES

(
    'Alimentação',
    'alimentacao',
    'utensils'
),

(
    'Bebidas',
    'bebidas',
    'cup'
),

(
    'Higiene Pessoal',
    'higiene-pessoal',
    'heart'
),

(
    'Limpeza',
    'limpeza',
    'sparkles'
),

(
    'Produtos para Bebé',
    'produtos-bebe',
    'baby'
),

(
    'Material Escolar',
    'material-escolar',
    'book'
),

(
    'Casa',
    'casa',
    'home'
),

(
    'Outros',
    'outros',
    'package'
);


-- ============================================================
-- 15. VIEW PARA COMPARAÇÃO DE PREÇOS
--
-- Mostra apenas:
-- - lojas aprovadas;
-- - ofertas activas;
-- - produtos activos;
-- - avaliações publicadas.
-- ============================================================

CREATE OR REPLACE VIEW vw_comparacao_precos AS

SELECT

    pl.id AS produto_loja_id,

    p.id AS produto_id,

    p.nome AS nome_produto,

    vp.id AS variacao_produto_id,

    vp.marca,

    vp.quantidade,

    vp.unidade_medida,

    vp.nome_variacao,

    pl.preco,

    pl.em_estoque,

    pl.ultima_atualizacao_preco,

    l.id AS loja_id,

    l.nome AS nome_loja,

    l.endereco,

    l.bairro,

    l.municipio,

    l.provincia,

    l.latitude,

    l.longitude,

    COALESCE(
        AVG(
            CASE
                WHEN ap.estado = 'publicada'
                THEN ap.classificacao
                ELSE NULL
            END
        ),
        0
    ) AS classificacao_media,

    COUNT(
        CASE
            WHEN ap.estado = 'publicada'
            THEN ap.id
            ELSE NULL
        END
    ) AS quantidade_avaliacoes

FROM produtos_lojas pl

INNER JOIN variacoes_produtos vp
    ON vp.id = pl.variacao_produto_id

INNER JOIN produtos p
    ON p.id = vp.produto_id

INNER JOIN lojas l
    ON l.id = pl.loja_id

LEFT JOIN avaliacoes_produtos ap
    ON ap.variacao_produto_id = vp.id
    AND ap.estado = 'publicada'

WHERE

    pl.estado = 'ativo'

    AND l.estado = 'aprovada'

    AND vp.estado = 'ativo'

GROUP BY

    pl.id,

    p.id,

    p.nome,

    vp.id,

    vp.marca,

    vp.quantidade,

    vp.unidade_medida,

    vp.nome_variacao,

    pl.preco,

    pl.em_estoque,

    pl.ultima_atualizacao_preco,

    l.id,

    l.nome,

    l.endereco,

    l.bairro,

    l.municipio,

    l.provincia,

    l.latitude,

    l.longitude;


-- ============================================================
-- 16. PROCEDURE:
-- ADICIONAR PRODUTO A UMA LOJA
--
-- Garante que:
-- - a loja pertence ao utilizador;
-- - o utilizador possui perfil de loja;
-- - o utilizador está activo;
-- - a loja está aprovada;
-- - o produto existe e está activo;
-- - o produto ainda não está cadastrado na loja.
-- ============================================================

DELIMITER $$

CREATE PROCEDURE sp_adicionar_produto_loja (

    IN p_utilizador_id INT UNSIGNED,

    IN p_loja_id INT UNSIGNED,

    IN p_variacao_produto_id INT UNSIGNED,

    IN p_preco DECIMAL(12,2),

    IN p_em_estoque BOOLEAN

)

BEGIN

    DECLARE v_autorizado INT DEFAULT 0;

    DECLARE v_existe INT DEFAULT 0;

    DECLARE v_novo_produto_loja_id INT UNSIGNED;


    -- Verificar se o utilizador está autorizado

    SELECT COUNT(*)

    INTO v_autorizado

    FROM lojas l

    INNER JOIN utilizadores u
        ON u.id = l.utilizador_id

    WHERE

        l.id = p_loja_id

        AND l.utilizador_id = p_utilizador_id

        AND u.perfil = 'loja'

        AND u.estado = 'ativo'

        AND l.estado = 'aprovada';


    IF v_autorizado = 0 THEN

        SIGNAL SQLSTATE '45000'

            SET MESSAGE_TEXT =
            'Operação recusada: a loja não está autorizada ou não pertence ao utilizador.';

    END IF;


    -- Verificar o preço

    IF p_preco < 0 THEN

        SIGNAL SQLSTATE '45000'

            SET MESSAGE_TEXT =
            'O preço não pode ser negativo.';

    END IF;


    -- Verificar se a variação existe e está activa

    SELECT COUNT(*)

    INTO v_existe

    FROM variacoes_produtos

    WHERE

        id = p_variacao_produto_id

        AND estado = 'ativo';


    IF v_existe = 0 THEN

        SIGNAL SQLSTATE '45000'

            SET MESSAGE_TEXT =
            'A variação do produto não existe ou está inactiva.';

    END IF;


    -- Verificar se o produto já está cadastrado

    SELECT COUNT(*)

    INTO v_existe

    FROM produtos_lojas

    WHERE

        loja_id = p_loja_id

        AND variacao_produto_id = p_variacao_produto_id;


    IF v_existe > 0 THEN

        SIGNAL SQLSTATE '45000'

            SET MESSAGE_TEXT =
            'Este produto já está cadastrado nesta loja. Use a rotina de actualização.';

    END IF;


    -- Inserir produto na loja

    INSERT INTO produtos_lojas (

        loja_id,

        variacao_produto_id,

        preco,

        em_estoque,

        estado,

        ultima_atualizacao_preco,

        ultima_atualizacao_estoque

    )

    VALUES (

        p_loja_id,

        p_variacao_produto_id,

        p_preco,

        p_em_estoque,

        'ativo',

        NOW(),

        NOW()

    );


    SET v_novo_produto_loja_id = LAST_INSERT_ID();


    -- Registar o primeiro preço no histórico

    INSERT INTO historico_precos (

        produto_loja_id,

        preco_anterior,

        novo_preco,

        registado_por_utilizador_id,

        registado_em

    )

    VALUES (

        v_novo_produto_loja_id,

        NULL,

        p_preco,

        p_utilizador_id,

        NOW()

    );


    -- Registar actividade

    INSERT INTO logs_atividades (

        utilizador_id,

        acao,

        detalhes

    )

    VALUES (

        p_utilizador_id,

        'CADASTRAR_PRODUTO_LOJA',

        CONCAT(

            'Variação do produto ',
            p_variacao_produto_id,

            ' cadastrada na loja ',
            p_loja_id,

            ' com preço ',
            p_preco,
            ' Kz.'

        )

    );

END$$


-- ============================================================
-- 17. PROCEDURE:
-- ACTUALIZAR PREÇO DE UMA LOJA
--
-- Apenas o proprietário da loja pode alterar o preço.
-- ============================================================

CREATE PROCEDURE sp_atualizar_preco_loja (

    IN p_utilizador_id INT UNSIGNED,

    IN p_produto_loja_id INT UNSIGNED,

    IN p_novo_preco DECIMAL(12,2)

)

BEGIN

    DECLARE v_autorizado INT DEFAULT 0;

    DECLARE v_preco_anterior DECIMAL(12,2);

    DECLARE v_loja_id INT UNSIGNED;


    -- Obter loja e preço actual

    SELECT

        pl.loja_id,

        pl.preco

    INTO

        v_loja_id,

        v_preco_anterior

    FROM produtos_lojas pl

    WHERE

        pl.id = p_produto_loja_id

    LIMIT 1;


    -- Verificar se a oferta existe

    IF v_loja_id IS NULL THEN

        SIGNAL SQLSTATE '45000'

            SET MESSAGE_TEXT =
            'Oferta da loja não encontrada.';

    END IF;


    -- Verificar autorização

    SELECT COUNT(*)

    INTO v_autorizado

    FROM lojas l

    INNER JOIN utilizadores u
        ON u.id = l.utilizador_id

    WHERE

        l.id = v_loja_id

        AND l.utilizador_id = p_utilizador_id

        AND u.perfil = 'loja'

        AND u.estado = 'ativo'

        AND l.estado = 'aprovada';


    IF v_autorizado = 0 THEN

        SIGNAL SQLSTATE '45000'

            SET MESSAGE_TEXT =
            'Operação recusada: apenas a loja autorizada pode alterar este preço.';

    END IF;


    -- Verificar preço

    IF p_novo_preco < 0 THEN

        SIGNAL SQLSTATE '45000'

            SET MESSAGE_TEXT =
            'O preço não pode ser negativo.';

    END IF;


    -- Actualizar apenas se o preço mudou

    IF p_novo_preco <> v_preco_anterior THEN

        UPDATE produtos_lojas

        SET

            preco = p_novo_preco,

            ultima_atualizacao_preco = NOW()

        WHERE

            id = p_produto_loja_id;


        -- Guardar histórico

        INSERT INTO historico_precos (

            produto_loja_id,

            preco_anterior,

            novo_preco,

            registado_por_utilizador_id,

            registado_em

        )

        VALUES (

            p_produto_loja_id,

            v_preco_anterior,

            p_novo_preco,

            p_utilizador_id,

            NOW()

        );


        -- Registar actividade

        INSERT INTO logs_atividades (

            utilizador_id,

            acao,

            detalhes

        )

        VALUES (

            p_utilizador_id,

            'ATUALIZAR_PRECO',

            CONCAT(

                'Oferta ',
                p_produto_loja_id,

                ': ',
                v_preco_anterior,

                ' Kz -> ',
                p_novo_preco,
                ' Kz.'

            )

        );

    END IF;

END$$


-- ============================================================
-- 18. PROCEDURE:
-- ACTUALIZAR STOCK
--
-- Apenas a loja autorizada pode alterar o stock.
-- ============================================================

CREATE PROCEDURE sp_atualizar_estoque_loja (

    IN p_utilizador_id INT UNSIGNED,

    IN p_produto_loja_id INT UNSIGNED,

    IN p_em_estoque BOOLEAN

)

BEGIN

    DECLARE v_autorizado INT DEFAULT 0;

    DECLARE v_loja_id INT UNSIGNED;


    -- Obter loja da oferta

    SELECT loja_id

    INTO v_loja_id

    FROM produtos_lojas

    WHERE

        id = p_produto_loja_id

    LIMIT 1;


    -- Verificar se a oferta existe

    IF v_loja_id IS NULL THEN

        SIGNAL SQLSTATE '45000'

            SET MESSAGE_TEXT =
            'Oferta da loja não encontrada.';

    END IF;


    -- Verificar autorização

    SELECT COUNT(*)

    INTO v_autorizado

    FROM lojas l

    INNER JOIN utilizadores u
        ON u.id = l.utilizador_id

    WHERE

        l.id = v_loja_id

        AND l.utilizador_id = p_utilizador_id

        AND u.perfil = 'loja'

        AND u.estado = 'ativo'

        AND l.estado = 'aprovada';


    IF v_autorizado = 0 THEN

        SIGNAL SQLSTATE '45000'

            SET MESSAGE_TEXT =
            'Operação recusada: apenas a loja autorizada pode alterar o estoque.';

    END IF;


    -- Actualizar estoque

    UPDATE produtos_lojas

    SET

        em_estoque = p_em_estoque,

        ultima_atualizacao_estoque = NOW()

    WHERE

        id = p_produto_loja_id;


    -- Registar actividade

    INSERT INTO logs_atividades (

        utilizador_id,

        acao,

        detalhes

    )

    VALUES (

        p_utilizador_id,

        'ATUALIZAR_ESTOQUE',

        CONCAT(

            'Estoque da oferta ',
            p_produto_loja_id,

            ' alterado para ',

            IF(
                p_em_estoque = TRUE,
                'DISPONÍVEL',
                'INDISPONÍVEL'
            ),

            '.'

        )

    );

END$$

DELIMITER ;


-- ============================================================
-- 19. EXEMPLO DE COMPARAÇÃO
--
-- Exemplo:
--
-- SELECT *
-- FROM vw_comparacao_precos
-- WHERE variacao_produto_id = 1
-- ORDER BY preco ASC;
--
-- ============================================================


-- ============================================================
-- 20. NOTAS PARA A APLICAÇÃO PHP
--
-- 1. Usar password_hash() para guardar palavras-passe.
--
-- 2. Para lojas, utilizar as procedures:
--
--    CALL sp_adicionar_produto_loja(...);
--
--    CALL sp_atualizar_preco_loja(...);
--
--    CALL sp_atualizar_estoque_loja(...);
--
-- 3. O cliente pode escolher a localização manualmente
--    ou permitir o uso do GPS.
--
-- 4. A distância pode ser calculada no PHP ou no SQL
--    utilizando latitude e longitude.
--
-- 5. A recomendação pode considerar:
--       - preço;
--       - distância;
--       - avaliação;
--       - disponibilidade em estoque.
--
-- 6. Evitar afirmar que um produto é "o melhor" de forma
--    absoluta. Mostrar os critérios utilizados.
--
-- 7. Testar primeiro numa base de dados de desenvolvimento.
--
-- ============================================================