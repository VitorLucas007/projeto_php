// Cálculos da Ficha de Composição Corporal (estilo InBody). Usado no
// formulário e, futuramente, por uma importação automática da InBody 270S.
// Faixas/valores proprietários da InBody (água, proteína, minerais, score,
// ângulo de fase, gordura visceral) não têm fórmula pública e ficam manuais.
(function (global) {
    'use strict';

    function paraNumero(v) {
        if (v === null || v === undefined || v === '') return null;
        const n = parseFloat(String(v).replace(',', '.'));
        return Number.isFinite(n) ? n : null;
    }

    function alturaEmMetros(alturaCm) {
        const a = paraNumero(alturaCm);
        return a ? a / 100 : null;
    }

    // IMC = peso(kg) / altura(m)^2
    function imc(pesoKg, alturaCm) {
        const peso = paraNumero(pesoKg);
        const altura = alturaEmMetros(alturaCm);
        if (!peso || !altura) return null;
        return peso / (altura * altura);
    }

    // Taxa Metabólica Basal — fórmula de Mifflin-St Jeor.
    function taxaMetabolicaBasal(pesoKg, alturaCm, idadeAnos, sexo) {
        const peso = paraNumero(pesoKg);
        const altura = paraNumero(alturaCm);
        const idade = paraNumero(idadeAnos);
        if (!peso || !altura || !idade) return null;

        const base = 10 * peso + 6.25 * altura - 5 * idade;
        return sexo === 'F' ? base - 161 : base + 5;
    }

    // Ingestão calórica recomendada = TMB x fator de atividade
    function ingestaoCalorica(tmb, fatorAtividade) {
        const t = paraNumero(tmb);
        const f = paraNumero(fatorAtividade);
        if (!t || !f) return null;
        return t * f;
    }

    // Peso ideal = IMC-alvo x altura(m)^2, IMC-alvo default 22
    function pesoIdeal(alturaCm, imcAlvo) {
        const altura = alturaEmMetros(alturaCm);
        const alvo = paraNumero(imcAlvo) || 22;
        if (!altura) return null;
        return alvo * altura * altura;
    }

    // Massa livre de gordura = peso - massa de gordura
    function massaLivreGordura(pesoKg, massaGorduraKg) {
        const peso = paraNumero(pesoKg);
        const gordura = paraNumero(massaGorduraKg);
        if (peso === null || gordura === null) return null;
        return peso - gordura;
    }

    // Grau de obesidade (%) = peso / peso ideal x 100
    function grauObesidade(pesoKg, pesoIdealKg) {
        const peso = paraNumero(pesoKg);
        const ideal = paraNumero(pesoIdealKg);
        if (!peso || !ideal) return null;
        return (peso / ideal) * 100;
    }

    // SMI = massa muscular esquelética(kg) / altura(m)^2
    function smi(mmeKg, alturaCm) {
        const mme = paraNumero(mmeKg);
        const altura = alturaEmMetros(alturaCm);
        if (!mme || !altura) return null;
        return mme / (altura * altura);
    }

    // Relação cintura-quadril = cintura(cm) / quadril(cm)
    function relacaoCinturaQuadril(cinturaCm, quadrilCm) {
        const cintura = paraNumero(cinturaCm);
        const quadril = paraNumero(quadrilCm);
        if (!cintura || !quadril) return null;
        return cintura / quadril;
    }

    // Controle de peso = ideal - atual. Controle de gordura = massa de gordura
    // ideal - atual. Controle muscular = controle de peso - controle de gordura.
    function controles(pesoKg, pesoIdealKg, massaGorduraKg, pgcAlvoPercentual) {
        const peso = paraNumero(pesoKg);
        const ideal = paraNumero(pesoIdealKg);
        const gordura = paraNumero(massaGorduraKg);
        const alvo = paraNumero(pgcAlvoPercentual);

        if (peso === null || ideal === null) {
            return { controlePeso: null, controleGordura: null, controleMuscular: null };
        }

        const controlePeso = ideal - peso;

        if (gordura === null || alvo === null) {
            return { controlePeso, controleGordura: null, controleMuscular: null };
        }

        const massaGorduraIdeal = ideal * (alvo / 100);
        const controleGordura = massaGorduraIdeal - gordura;
        const controleMuscular = controlePeso - controleGordura;

        return { controlePeso, controleGordura, controleMuscular };
    }

    // Posição na barra (estilo InBody), com 100% = centro da faixa normal.
    // Aproximação — a InBody usa uma curva proprietária.
    function percentualNaFaixa(valor, min, max) {
        const v = paraNumero(valor);
        const mn = paraNumero(min);
        const mx = paraNumero(max);
        if (v === null || !mn || !mx) return null;
        const centro = (mn + mx) / 2;
        if (!centro) return null;
        return (v / centro) * 100;
    }

    // Idade em anos completos, a partir do nascimento e da data do teste
    function idade(dataNascimentoISO, dataReferenciaISO) {
        if (!dataNascimentoISO) return null;
        const nascimento = new Date(dataNascimentoISO);
        const referencia = dataReferenciaISO ? new Date(dataReferenciaISO) : new Date();
        if (isNaN(nascimento.getTime()) || isNaN(referencia.getTime())) return null;

        let anos = referencia.getFullYear() - nascimento.getFullYear();
        const m = referencia.getMonth() - nascimento.getMonth();
        if (m < 0 || (m === 0 && referencia.getDate() < nascimento.getDate())) {
            anos--;
        }
        return anos;
    }

    // Faixas normais sugeridas (editáveis). IMC pela OMS, %gordura pelo ACE.
    const FAIXAS_PADRAO = {
        imc: { min: 18.5, max: 24.9 },
        pgc: {
            M: { min: 10, max: 20 },
            F: { min: 18, max: 28 },
        },
        grauObesidade: { min: 90, max: 110 },
    };

    // METs por esporte (Compendium of Physical Activities). kcal = MET x peso x horas.
    const MET_ESPORTES = {
        'Golfe': 4.8,
        'Gate-ball': 4.0,
        'Caminhada': 3.5,
        'Ioga': 3.0,
        'Badminton': 5.5,
        'Tênis de mesa': 4.0,
        'Tênis': 7.3,
        'Ciclismo': 7.5,
        'Boxe': 7.8,
        'Basquetebol': 8.0,
        'Escalada': 8.0,
        'Aeróbica': 7.3,
        'Jogging': 7.0,
        'Futebol': 7.0,
        'Natação': 6.0,
        'Esgrima japonesa': 6.0,
        'Raquetebol': 7.0,
        'Squash': 12.0,
        'Taekwondo': 10.3,
        'Pular corda': 10.0,
    };

    function gastoCaloricoPorEsporte(pesoKg, minutos) {
        const peso = paraNumero(pesoKg);
        const tempoHoras = (paraNumero(minutos) || 30) / 60;
        if (!peso) return {};

        const resultado = {};
        Object.keys(MET_ESPORTES).forEach(function (esporte) {
            resultado[esporte] = Math.round(MET_ESPORTES[esporte] * peso * tempoHoras);
        });
        return resultado;
    }

    global.ComposicaoCalc = {
        paraNumero,
        imc,
        taxaMetabolicaBasal,
        ingestaoCalorica,
        pesoIdeal,
        massaLivreGordura,
        grauObesidade,
        smi,
        relacaoCinturaQuadril,
        controles,
        percentualNaFaixa,
        idade,
        gastoCaloricoPorEsporte,
        FAIXAS_PADRAO,
        MET_ESPORTES,
    };

})(window);
