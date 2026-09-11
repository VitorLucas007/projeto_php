-- Migração: cria a tabela ficha_composicao_corporal (ficha de bioimpedância,
-- estilo InBody, vinculada a uma avaliação já existente).
-- Rodar em bancos já existentes (quem for criar o banco do zero, já usa
-- o script_pibeu_definitivo_2026.sql atualizado e não precisa rodar isso).

CREATE TABLE IF NOT EXISTS ficha_composicao_corporal (
    id_ficha INT AUTO_INCREMENT PRIMARY KEY,
    fk_avaliacao INT NOT NULL,
    fk_professor INT NOT NULL,
    data_teste DATE NOT NULL,
    hora_teste TIME,
    id_equipamento VARCHAR(50),

    altura DECIMAL(5,2),
    sexo_referencia ENUM('M', 'F'),
    idade_referencia INT,

    agua_corporal_total DECIMAL(5,2),
    agua_corporal_min DECIMAL(5,2),
    agua_corporal_max DECIMAL(5,2),

    proteina DECIMAL(5,2),
    proteina_min DECIMAL(5,2),
    proteina_max DECIMAL(5,2),

    minerais DECIMAL(5,2),
    minerais_min DECIMAL(5,2),
    minerais_max DECIMAL(5,2),

    massa_gordura DECIMAL(5,2),
    massa_gordura_min DECIMAL(5,2),
    massa_gordura_max DECIMAL(5,2),

    peso DECIMAL(5,2),
    peso_min DECIMAL(5,2),
    peso_max DECIMAL(5,2),

    massa_muscular_esqueletica DECIMAL(5,2),
    mme_min DECIMAL(5,2),
    mme_max DECIMAL(5,2),

    imc DECIMAL(5,2),
    imc_min DECIMAL(5,2),
    imc_max DECIMAL(5,2),

    percentual_gordura_corporal DECIMAL(5,2),
    pgc_min DECIMAL(5,2),
    pgc_max DECIMAL(5,2),

    peso_ideal DECIMAL(5,2),
    controle_peso DECIMAL(5,2),
    controle_gordura DECIMAL(5,2),
    controle_muscular DECIMAL(5,2),
    pontuacao_geral INT,

    relacao_cintura_quadril DECIMAL(4,2),
    nivel_gordura_visceral INT,
    angulo_fase DECIMAL(4,2),

    massa_livre_gordura DECIMAL(5,2),
    mlg_min DECIMAL(5,2),
    mlg_max DECIMAL(5,2),

    fator_atividade DECIMAL(3,2),
    taxa_metabolica_basal DECIMAL(6,2),
    tmb_min DECIMAL(6,2),
    tmb_max DECIMAL(6,2),

    grau_obesidade DECIMAL(5,2),
    grau_obesidade_min DECIMAL(5,2),
    grau_obesidade_max DECIMAL(5,2),

    smi DECIMAL(5,2),
    ingestao_calorica_recomendada DECIMAL(6,2),

    seg_magra_braco_esq_kg DECIMAL(5,2),
    seg_magra_braco_esq_pct DECIMAL(5,2),
    seg_magra_braco_dir_kg DECIMAL(5,2),
    seg_magra_braco_dir_pct DECIMAL(5,2),
    seg_magra_tronco_kg DECIMAL(5,2),
    seg_magra_tronco_pct DECIMAL(5,2),
    seg_magra_perna_esq_kg DECIMAL(5,2),
    seg_magra_perna_esq_pct DECIMAL(5,2),
    seg_magra_perna_dir_kg DECIMAL(5,2),
    seg_magra_perna_dir_pct DECIMAL(5,2),

    seg_gordura_braco_esq_kg DECIMAL(5,2),
    seg_gordura_braco_esq_pct DECIMAL(5,2),
    seg_gordura_braco_dir_kg DECIMAL(5,2),
    seg_gordura_braco_dir_pct DECIMAL(5,2),
    seg_gordura_tronco_kg DECIMAL(5,2),
    seg_gordura_tronco_pct DECIMAL(5,2),
    seg_gordura_perna_esq_kg DECIMAL(5,2),
    seg_gordura_perna_esq_pct DECIMAL(5,2),
    seg_gordura_perna_dir_kg DECIMAL(5,2),
    seg_gordura_perna_dir_pct DECIMAL(5,2),

    observacoes TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_ficha_composicao_avaliacao FOREIGN KEY (fk_avaliacao) REFERENCES avaliacao(id_avaliacao) ON DELETE CASCADE,
    CONSTRAINT fk_ficha_composicao_professor FOREIGN KEY (fk_professor) REFERENCES professor(id_professor)
);
