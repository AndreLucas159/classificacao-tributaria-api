<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Secao;
use App\Models\Produto;
use App\Models\RegraTributaria;

class ProdutosSeeder extends Seeder
{
    /**
     * Executa o seeder para popular o banco de dados.
     *
     * @return void
     */
    public function run(): void
    {
        // Desativa a verificação de chaves estrangeiras de forma compatível com múltiplos bancos.
        // Schema::disableForeignKeyConstraints();

        // Limpa as tabelas para evitar duplicidade de dados em reexecuções.
        RegraTributaria::truncate();
        Produto::truncate();
        Secao::truncate();

        // ------------------------------------------------------------------------------------
        // SEÇÃO 1: AUTOPEÇAS
        // ------------------------------------------------------------------------------------
        $secaoAutos = Secao::create(['nome' => '1 - AUTOPEÇAS']);

        $produtosAutos = [
            ['item' => '1.0', 'cest' => '01.001.00', 'ncm_sh' => '3815.12.10 / 3815.12.90', 'descricao' => 'Catalisadores em colmeia cerâmica ou metálica para conversão catalítica de gases de escape de veículos e outros catalisadores'],
            ['item' => '2.0', 'cest' => '01.002.00', 'ncm_sh' => '3917', 'descricao' => 'Tubos e seus acessórios (por exemplo, juntas, cotovelos, flanges, uniões), de plásticos'],
            ['item' => '3.0', 'cest' => '01.003.00', 'ncm_sh' => '3918.10.00', 'descricao' => 'Protetores de caçamba'],
            ['item' => '4.0', 'cest' => '01.004.00', 'ncm_sh' => '3923.30.00', 'descricao' => 'Reservatórios de óleo'],
            ['item' => '5.0', 'cest' => '01.005.00', 'ncm_sh' => '3926.30.00', 'descricao' => 'Frisos, decalques, molduras e acabamentos'],
            ['item' => '999.0', 'cest' => '01.999.00', 'ncm_sh' => '', 'descricao' => 'Outras peças, partes e acessórios para veículos automotores não relacionados nos demais itens deste anexo'],
        ];

        foreach ($produtosAutos as $produtoData) {
            $produto = Produto::create([
                'secao_id' => $secaoAutos->id,
                'item' => $produtoData['item'],
                'cest' => $produtoData['cest'],
                'ncm_sh' => $produtoData['ncm_sh'],
                'descricao' => $produtoData['descricao'],
            ]);

            $regrasFidelidade = [
                ['aliquota_interestadual' => 12, 'mva_original' => 36.56, 'multiplicador_original' => 13.95, 'mva_ajustada' => 48.36, 'multiplicador_ajustado' => 16.19],
                ['aliquota_interestadual' => 7, 'mva_original' => 36.56, 'multiplicador_original' => 18.95, 'mva_ajustada' => 56.79, 'multiplicador_ajustado' => 22.79],
                ['aliquota_interestadual' => 4, 'mva_original' => 36.56, 'multiplicador_original' => 21.95, 'mva_ajustada' => 61.85, 'multiplicador_ajustado' => 26.75],
                ['aliquota_interestadual' => 12, 'mva_original' => 36.56, 'multiplicador_original' => 20.64, 'mva_ajustada' => 86.63, 'multiplicador_ajustado' => 23.46],
            ];
            foreach($regrasFidelidade as $regra) {
                RegraTributaria::create(array_merge($regra, ['produto_id' => $produto->id, 'ato_legal' => 'Protocolo ICMS 41/2008 e Convênio ICMS 142/2018', 'aliquota_interna' => 19.00, 'descricao_extra' => 'Índice de Fidelidade']));
            }

            $regrasDemais = [
                ['aliquota_interestadual' => 7, 'mva_original' => 71.78, 'multiplicador_original' => 25.64, 'mva_ajustada' => 103.59, 'multiplicador_ajustado' => 30.47],
                ['aliquota_interestadual' => 4, 'mva_original' => 71.78, 'multiplicador_original' => 28.64, 'mva_ajustada' => 103.59, 'multiplicador_ajustado' => 34.68],
            ];
            foreach($regrasDemais as $regra) {
                RegraTributaria::create(array_merge($regra, ['produto_id' => $produto->id, 'ato_legal' => 'Protocolo ICMS 41/2008 e Convênio ICMS 142/2018', 'aliquota_interna' => 19.00, 'descricao_extra' => 'Demais Casos (não inclusos no índice de fidelidade)']));
            }
        }

        // ------------------------------------------------------------------------------------
        // SEÇÃO 2: BEBIDAS ALCOÓLICAS, EXCETO CERVEJA E CHOPE
        // ------------------------------------------------------------------------------------
        $secaoBebidas = Secao::create(['nome' => '2 - BEBIDAS ALCOÓLICAS, EXCETO CERVEJA E CHOPE']);

        $produtosBebidas = [
            ['item' => '1.0', 'cest' => '02.001.00', 'ncm_sh' => '2205 / 2208.90.00', 'descricao' => 'Aperitivos, amargos, bitter e similares'],
            ['item' => '2.0', 'cest' => '02.002.00', 'ncm_sh' => '2208.90.00', 'descricao' => 'Batida e similares'],
            ['item' => '3.0', 'cest' => '02.003.00', 'ncm_sh' => '2208.90.00', 'descricao' => 'Bebida ice'],
            ['item' => '4.0', 'cest' => '02.004.00', 'ncm_sh' => '2207.20 / 2208.40.00', 'descricao' => 'Cachaça e aguardentes'],
        ];

        $regrasBebidas = [
            ['aliquota_interestadual' => 12, 'multiplicador_original' => 54.00],
            ['aliquota_interestadual' => 7, 'multiplicador_original' => 59.00],
            ['aliquota_interestadual' => 4, 'multiplicador_original' => 62.00],
        ];

        foreach ($produtosBebidas as $produtoData) {
            $produto = Produto::create([
                'secao_id' => $secaoBebidas->id,
                'item' => $produtoData['item'],
                'cest' => $produtoData['cest'],
                'ncm_sh' => $produtoData['ncm_sh'],
                'descricao' => $produtoData['descricao'],
            ]);

            foreach($regrasBebidas as $regra) {
                RegraTributaria::create(array_merge($regra, ['produto_id' => $produto->id, 'ato_legal' => 'Antecipação com Encerramento de Tributação', 'aliquota_interna' => 33.00, 'mva_original' => 100.00]));
            }
        }

        // ------------------------------------------------------------------------------------
        // SEÇÃO 3: CERVEJAS, CHOPES, REFRIGERANTES, ÁGUAS E OUTRAS BEBIDAS
        // ------------------------------------------------------------------------------------
        $secaoCervejas = Secao::create(['nome' => '3 - CERVEJAS, CHOPES, REFRIGERANTES, ÁGUAS E OUTRAS BEBIDAS']);

        $produtosCervejas = [
             ['item' => '3.0', 'cest' => '03.003.00', 'ncm_sh' => '2201.10.00', 'descricao' => 'Água mineral, gasosa ou não, ou potável, naturais, em embalagem de vidro descartável', 'aliquota_interna' => 25.00, 'mva_original' => 80.00],
             ['item' => '10.0', 'cest' => '03.010.00', 'ncm_sh' => '2202.10.00 / 2202.99.00', 'descricao' => 'Refrigerante em vidro descartável', 'aliquota_interna' => 25.00, 'mva_original' => 75.00],
             ['item' => '21.0', 'cest' => '03.021.00', 'ncm_sh' => '2203.00.00', 'descricao' => 'Cerveja em garrafa de vidro retornável', 'aliquota_interna' => 27.00, 'mva_original' => 140.00],
             ['item' => '23.0', 'cest' => '03.023.00', 'ncm_sh' => '2203.00.00', 'descricao' => 'Chope', 'aliquota_interna' => 27.00, 'mva_original' => 140.00],
        ];

        $regrasCervejas = [
            '03.003.00' => [
                ['aliquota_interestadual' => 12, 'multiplicador_original' => 33.00],
                ['aliquota_interestadual' => 7, 'multiplicador_original' => 38.00],
                ['aliquota_interestadual' => 4, 'multiplicador_original' => 41.00],
            ],
            '03.010.00' => [
                ['aliquota_interestadual' => 12, 'multiplicador_original' => 31.75],
                ['aliquota_interestadual' => 7, 'multiplicador_original' => 36.75],
                ['aliquota_interestadual' => 4, 'multiplicador_original' => 39.75],
            ],
            '03.021.00' => [
                ['aliquota_interestadual' => 12, 'multiplicador_original' => 52.80],
                ['aliquota_interestadual' => 7, 'multiplicador_original' => 57.80],
                ['aliquota_interestadual' => 4, 'multiplicador_original' => 60.80],
            ],
             '03.023.00' => [
                ['aliquota_interestadual' => 12, 'multiplicador_original' => 52.80],
                ['aliquota_interestadual' => 7, 'multiplicador_original' => 57.80],
                ['aliquota_interestadual' => 4, 'multiplicador_original' => 60.80],
            ],
        ];

        foreach ($produtosCervejas as $produtoData) {
            $produto = Produto::create([
                'secao_id' => $secaoCervejas->id,
                'item' => $produtoData['item'],
                'cest' => $produtoData['cest'],
                'ncm_sh' => $produtoData['ncm_sh'],
                'descricao' => $produtoData['descricao'],
            ]);

            if(isset($regrasCervejas[$produto->cest])) {
                foreach($regrasCervejas[$produto->cest] as $regra) {
                    RegraTributaria::create(array_merge($regra, [
                        'produto_id' => $produto->id,
                        'ato_legal' => 'Protocolos ICMS 11/91, 10/92 e Convênio ICMS 142/2018',
                        'aliquota_interna' => $produtoData['aliquota_interna'],
                        'mva_original' => $produtoData['mva_original']
                    ]));
                }
            }
        }

        // ------------------------------------------------------------------------------------
        // SEÇÃO 4: CIGARROS E OUTROS PRODUTOS DERIVADOS DO FUMO
        // ------------------------------------------------------------------------------------
        $secaoCigarros = Secao::create(['nome' => '4 - CIGARROS E OUTROS PRODUTOS DERIVADOS DO FUMO']);

        $produtosCigarros = [
            ['item' => '1.0', 'cest' => '04.001.00', 'ncm_sh' => '2402', 'descricao' => 'Charutos, cigarrilhas e cigarros, de tabaco ou dos seus sucedâneos'],
            ['item' => '2.0', 'cest' => '04.002.00', 'ncm_sh' => '2403.1', 'descricao' => 'Tabaco para fumar, mesmo contendo sucedâneos de tabaco em qualquer proporção'],
        ];

        $regrasCigarros = [
            ['aliquota_interestadual' => 12, 'multiplicador_original' => 33.00, 'mva_ajustada' => 88.57, 'multiplicador_ajustado' => 44.57],
            ['aliquota_interestadual' => 7, 'multiplicador_original' => 38.00, 'mva_ajustada' => 99.29, 'multiplicador_ajustado' => 52.79],
            ['aliquota_interestadual' => 4, 'multiplicador_original' => 41.00, 'mva_ajustada' => 105.71, 'multiplicador_ajustado' => 57.71],
        ];

        foreach ($produtosCigarros as $produtoData) {
            $produto = Produto::create([
                'secao_id' => $secaoCigarros->id,
                'item' => $produtoData['item'],
                'cest' => $produtoData['cest'],
                'ncm_sh' => $produtoData['ncm_sh'],
                'descricao' => $produtoData['descricao'],
            ]);
            foreach ($regrasCigarros as $regra) {
                RegraTributaria::create(array_merge($regra, ['produto_id' => $produto->id, 'ato_legal' => 'Convênios ICMS 111/2017 e 142/2018', 'aliquota_interna' => 30.00, 'mva_original' => 50.00]));
            }
        }

        // ------------------------------------------------------------------------------------
        // SEÇÃO 5: CIMENTOS
        // ------------------------------------------------------------------------------------
        $secaoCimentos = Secao::create(['nome' => '5 - CIMENTOS']);

        $produtoCimento = Produto::create(['secao_id' => $secaoCimentos->id, 'item' => '1.0', 'cest' => '05.001.00', 'ncm_sh' => '2523', 'descricao' => 'Cimento']);

        $regrasCimento = [
            ['aliquota_interestadual' => 12, 'multiplicador_original' => 10.80, 'mva_ajustada' => 30.37, 'multiplicador_ajustado' => 12.77],
            ['aliquota_interestadual' => 7, 'multiplicador_original' => 15.80, 'mva_ajustada' => 37.78, 'multiplicador_ajustado' => 19.18],
            ['aliquota_interestadual' => 4, 'multiplicador_original' => 18.80, 'mva_ajustada' => 42.22, 'multiplicador_ajustado' => 23.02],
        ];

        foreach ($regrasCimento as $regra) {
            RegraTributaria::create(array_merge($regra, ['produto_id' => $produtoCimento->id, 'ato_legal' => 'Protocolo ICMS 11/85 e Convênio ICMS 142/2018', 'aliquota_interna' => 19.00, 'mva_original' => 20.00]));
        }

        // ------------------------------------------------------------------------------------
        // SEÇÃO 6: COMBUSTÍVEIS E LUBRIFICANTES
        // ------------------------------------------------------------------------------------
        $secaoCombustiveis = Secao::create(['nome' => '6 - COMBUSTÍVEIS E LUBRIFICANTES']);

        $produtosCombustiveis = [
            ['item' => '1.0', 'cest' => '06.001.00', 'ncm_sh' => '2207.10.10', 'descricao' => 'Álcool etílico anidro combustível'],
            ['item' => '2.0', 'cest' => '06.002.00', 'ncm_sh' => '2710.12.59', 'descricao' => 'Gasolina automotiva A, exceto Premium'],
            ['item' => '7.0', 'cest' => '06.007.00', 'ncm_sh' => '2710.19.3', 'descricao' => 'Óleos lubrificantes'],
            ['item' => '8.1', 'cest' => '06.008.01', 'ncm_sh' => '2710.19.9', 'descricao' => 'Graxa lubrificante'],
        ];

        foreach ($produtosCombustiveis as $produtoData) {
            $produto = Produto::create([
                'secao_id' => $secaoCombustiveis->id,
                'item' => $produtoData['item'],
                'cest' => $produtoData['cest'],
                'ncm_sh' => $produtoData['ncm_sh'],
                'descricao' => $produtoData['descricao'],
            ]);

            if ($produto->cest == '06.007.00' || $produto->cest == '06.008.01') {
                $regras = [
                    ['aliquota_interestadual' => 12, 'mva_ajustada' => 99.15, 'multiplicador_ajustado' => 37.84],
                    ['aliquota_interestadual' => 7, 'mva_ajustada' => 99.15, 'multiplicador_ajustado' => 37.84],
                    ['aliquota_interestadual' => 4, 'mva_ajustada' => 99.15, 'multiplicador_ajustado' => 37.84],
                ];
                $ato_legal = ($produto->cest == '06.007.00') ? 'Convênio ICMS 110/07' : 'Ato Cotepe/MVA';
                foreach ($regras as $regra) {
                    RegraTributaria::create(array_merge($regra, [
                        'produto_id' => $produto->id,
                        'aliquota_interna' => 19.00,
                        'mva_original' => 61.31,
                        'multiplicador_original' => 30.65
                    ]));
                }
            } elseif ($produto->cest == '06.001.00' || $produto->cest == '06.002.00') {
                 RegraTributaria::create([
                    'produto_id' => $produto->id,
                    'aliquota_interna' => 0,
                    'aliquota_interestadual' => 0,
                    'descricao_extra' => 'Base de cálculo é o Preço Médio Ponderado a Consumidor Final (PMPF)'
                 ]);
            }
        }

        // ------------------------------------------------------------------------------------
        // SEÇÃO 8: FERRAMENTAS
        // ------------------------------------------------------------------------------------
        $secaoFerramentas = Secao::create(['nome' => '8 - FERRAMENTAS']);

        $produtosFerramentas = [
            ['item' => '1.0', 'cest' => '08.001.00', 'ncm_sh' => '4016.99.90', 'descricao' => 'Ferramentas de borracha vulcanizada não endurecida'],
            ['item' => '5.0', 'cest' => '08.005.00', 'ncm_sh' => '8202.20.00', 'descricao' => 'Folhas de serras de fita'],
            ['item' => '8.0', 'cest' => '08.008.00', 'ncm_sh' => '8203', 'descricao' => 'Limas, grosas, alicates (mesmo cortantes), tenazes, pinças, cisalhas para metais, corta-tubos, corta-pinos, saca-bocados e ferramentas semelhantes, manuais'],
        ];

        $regrasFerramentas = [
            ['aliquota_interestadual' => 12, 'multiplicador_original' => 16.50],
            ['aliquota_interestadual' => 7, 'multiplicador_original' => 21.50],
            ['aliquota_interestadual' => 4, 'multiplicador_original' => 24.50],
        ];

        foreach($produtosFerramentas as $produtoData) {
            $produto = Produto::create([
                'secao_id' => $secaoFerramentas->id,
                'item' => $produtoData['item'],
                'cest' => $produtoData['cest'],
                'ncm_sh' => $produtoData['ncm_sh'],
                'descricao' => $produtoData['descricao'],
            ]);
            foreach($regrasFerramentas as $regra) {
                RegraTributaria::create(array_merge($regra, ['produto_id' => $produto->id, 'ato_legal' => 'Antecipação com Encerramento de Tributação', 'aliquota_interna' => 19.00, 'mva_original' => 45.00]));
            }
        }

        // ------------------------------------------------------------------------------------
        // SEÇÃO 9: LÂMPADAS, REATORES E "STARTER"
        // ------------------------------------------------------------------------------------
        $secaoLampadas = Secao::create(['nome' => '9 - LÂMPADAS, REATORES E "STARTER"']);

        $produtoLampada = Produto::create(['secao_id' => $secaoLampadas->id, 'item' => '1.0', 'cest' => '09.001.00', 'ncm_sh' => '8539', 'descricao' => 'Lâmpadas elétricas']);

        $regrasLampada = [
            ['aliquota_interestadual' => 12, 'mva_original' => 60.03, 'multiplicador_original' => 18.41, 'mva_ajustada' => 73.86, 'multiplicador_ajustado' => 21.03],
            ['aliquota_interestadual' => 7, 'mva_original' => 60.03, 'multiplicador_original' => 23.41, 'mva_ajustada' => 83.74, 'multiplicador_ajustado' => 27.91],
            ['aliquota_interestadual' => 4, 'mva_original' => 60.03, 'multiplicador_original' => 26.41, 'mva_ajustada' => 89.67, 'multiplicador_ajustado' => 32.04],
        ];

        foreach($regrasLampada as $regra) {
            RegraTributaria::create(array_merge($regra, ['produto_id' => $produtoLampada->id, 'ato_legal' => 'Protocolo ICMS 17/85 e Convênio ICMS 142/2018', 'aliquota_interna' => 19.00]));
        }

        // ------------------------------------------------------------------------------------
        // SEÇÃO 10: MATERIAIS DE CONSTRUÇÃO E CONGÊNERES
        // ------------------------------------------------------------------------------------
        $secaoConstrucao = Secao::create(['nome' => '10 - MATERIAIS DE CONSTRUÇÃO E CONGÊNERES']);

        $produtoCal = Produto::create(['secao_id' => $secaoConstrucao->id, 'item' => '1.0', 'cest' => '10.001.00', 'ncm_sh' => '2522', 'descricao' => 'Cal']);

        $regrasCal = [
            ['aliquota_interestadual' => 12, 'multiplicador_original' => 14.60, 'mva_ajustada' => 52.10, 'multiplicador_ajustado' => 16.90],
            ['aliquota_interestadual' => 7, 'multiplicador_original' => 19.60, 'mva_ajustada' => 60.74, 'multiplicador_ajustado' => 23.54],
            ['aliquota_interestadual' => 4, 'multiplicador_original' => 22.60, 'mva_ajustada' => 65.93, 'multiplicador_ajustado' => 27.53],
        ];

        foreach($regrasCal as $regra) {
            RegraTributaria::create(array_merge($regra, ['produto_id' => $produtoCal->id, 'ato_legal' => 'Protocolo ICMS 85/2011 e Convênio ICMS 142/2018', 'aliquota_interna' => 19.00, 'mva_original' => 40.00]));
        }

        // ------------------------------------------------------------------------------------
        // SEÇÃO 11: MATERIAIS DE LIMPEZA
        // ------------------------------------------------------------------------------------
        $secaoLimpeza = Secao::create(['nome' => '11 - MATERIAIS DE LIMPEZA']);

        $produtosLimpeza = [
            ['item' => '1.0', 'cest' => '11.001.00', 'ncm_sh' => '2828.90.11 / 2828.90.19 / 3206.41.00 / 3402.50.00 / 3808.94.19', 'descricao' => 'Água sanitária, branqueador e outros alvejantes'],
            ['item' => '8.0', 'cest' => '11.008.00', 'ncm_sh' => '3809.91.90', 'descricao' => 'Amaciante/suavizante'],
        ];

        $regrasLimpeza = [
            ['aliquota_interestadual' => 12, 'multiplicador_original' => 15.55],
            ['aliquota_interestadual' => 7, 'multiplicador_original' => 20.55],
            ['aliquota_interestadual' => 4, 'multiplicador_original' => 23.55],
        ];

        foreach($produtosLimpeza as $produtoData) {
            $produto = Produto::create([
                'secao_id' => $secaoLimpeza->id,
                'item' => $produtoData['item'],
                'cest' => $produtoData['cest'],
                'ncm_sh' => $produtoData['ncm_sh'],
                'descricao' => $produtoData['descricao'],
            ]);
            foreach($regrasLimpeza as $regra) {
                RegraTributaria::create(array_merge($regra, ['produto_id' => $produto->id, 'ato_legal' => 'Antecipação com Encerramento de Tributação', 'aliquota_interna' => 19.00, 'mva_original' => 45.00]));
            }
        }

        // ------------------------------------------------------------------------------------
        // SEÇÃO 12: MATERIAIS ELÉTRICOS
        // ------------------------------------------------------------------------------------
        $secaoEletricos = Secao::create(['nome' => '12 - MATERIAIS ELÉTRICOS']);

        $produtoChuveiro = Produto::create(['secao_id' => $secaoEletricos->id, 'item' => '2.0', 'cest' => '12.002.00', 'ncm_sh' => '8516', 'descricao' => 'Aquecedores elétricos de água, incluídos os de imersão, chuveiros ou duchas elétricas, torneiras elétricas...']);

        $regrasChuveiro = [
            ['aliquota_interestadual' => 12, 'multiplicador_original' => 14.03, 'mva_ajustada' => 48.84, 'multiplicador_ajustado' => 16.28],
            ['aliquota_interestadual' => 7, 'multiplicador_original' => 19.03, 'mva_ajustada' => 57.30, 'multiplicador_ajustado' => 22.89],
            ['aliquota_interestadual' => 4, 'multiplicador_original' => 22.03, 'mva_ajustada' => 62.37, 'multiplicador_ajustado' => 26.85],
        ];

        foreach($regrasChuveiro as $regra) {
            RegraTributaria::create(array_merge($regra, ['produto_id' => $produtoChuveiro->id, 'ato_legal' => 'Protocolo ICMS 84/2011 e Convênio ICMS 142/2018', 'aliquota_interna' => 19.00, 'mva_original' => 37.00]));
        }

        // Reativa a verificação de chaves estrangeiras.
        // Schema::enableForeignKeyConstraints();
    }
}