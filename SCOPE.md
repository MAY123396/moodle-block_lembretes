# Escopo do Projeto — Lembretes de tarefas (`block_lembretes`)

> Copie este arquivo para `SCOPE.md` na raiz do seu plugin e preencha. É um documento
> **interno** de planejamento (em pt-BR) — não confundir com o `README.md` público.
> Ele fica **versionado** (o professor corrige pelo repositório) mas **fora do zip
> publicado** — o `plugin-new` já configura o `.gitattributes` (`export-ignore`) para isso.

> [!NOTE]
> **Regras de código (para você e para a IA):**
> - `.github/copilot-instructions.md` (na raiz deste plugin) — regras essenciais que o
>   Copilot carrega em todo prompt.
> - `.github/instructions/*.instructions.md` — regras profundas por área (banco, backup,
>   privacidade, front-end, atividade, i18n, testes), carregadas automaticamente quando você
>   edita o arquivo correspondente.
> - `docs/REGRAS-DE-CODIGO.md` **do laboratório** — a referência completa e comentada.
>
> **Ambiente de desenvolvimento:** este plugin é desenvolvido no Codespace **Moodle Plugin
> Lab**. Os comandos essenciais estão na seção "Ambiente" do `.github/copilot-instructions.md`
> (aqui na raiz do plugin); a referência completa em `docs/AMBIENTE.md` **do laboratório**.
> Fora do Codespace do lab esses comandos não existem.

> [!NOTE]
> **Frankenstyle** (`type_name`): curto, em **inglês**, minúsculo, de preferência um único
> token, **verificado livre no Moodle Plugins Directory** antes de fixar (página 404 +
> `download.moodle.org/api/1.3/pluginfo.php?plugin=type_name` retorna "não encontrado").
> Renomear depois é caro (cascateia em tabelas, capabilities, chaves de lang, caminhos).
> O **Nome de exibição** (string `pluginname`) é separado e **localizável** — pode ser em
> português.

- **Versão do documento:** 1.0
- **Data:** 07/10/2026
- **Autor:** Maysa
- **Tipo de plugin:** block_
- **Compatibilidade alvo:** Moodle 4.5 e 5.x (validar no CI)
- **PHP mínimo:** 8.1 (mínimo do Moodle 4.5; validar no CI)
- **Resumo:** Bloco para o Painel do Moodle que ajuda estudantes a encontrar tarefas ainda não enviadas
  com prazo nos próximos cinco dias, em Moodle 4.5 e 5.x.
- **Licença:** GPL v3+

---

## 1. Contextualização e Objetivos

- **Problema:** Estudantes podem esquecer tarefas com prazo próximo em diferentes cursos.
- **Solução:** Exibir no Painel uma lista de tarefas futuras ainda não enviadas, com links para abri-las.
- **Público-alvo:** Estudantes matriculados em cursos do Moodle.
- **Justificativa:** Reunir em um só lugar os prazos próximos sem exigir que o estudante abra cada curso.
- **Distribuição:** Uso acadêmico no Moodle Plugin Lab; licença GPL v3+.

---

## 2. Análise de Alternativas (Estado da Arte)

> Preencher **antes** de detalhar o escopo. Se algo já resolve bem o problema, a decisão
> certa pode ser usar/estender, não construir do zero. Fontes: Plugins Directory,
> funcionalidades do core, plugins já instalados, processo manual atual.

| Solução existente | Tipo | O que faz | Limitação / lacuna |
|---|---|---|---|
| Bloco Timeline do Moodle | Core | Mostra atividades e prazos próximos do estudante. | Este plugin propõe uma lista focada somente em tarefas não enviadas nos próximos cinco dias. |
| Calendário do Moodle | Core | Mostra eventos e datas dos cursos. | Exige que o estudante consulte o calendário; não é uma lista dedicada a tarefas pendentes. |

- **Diferencial do plugin:** filtro simples por tarefa pendente e prazo nos próximos cinco dias; acesso direto à atividade.
- **Veredito (build vs. reuse):** construir para praticar o desenvolvimento de blocos e atender ao recorte definido. Para uso real, comparar com o Timeline nativo antes de instalar outro bloco semelhante.

---

## 3. Escopo, Versionamento e Limites

### 3.1. Escopo da V1 (MVP publicável)

O MVP é somente leitura e não terá configurações para o estudante.
- Mostrar no Painel tarefas do tipo Tarefa (`mod_assign`) dos cursos em que o estudante está matriculado.
- Incluir tarefas com prazo futuro entre o momento atual e os próximos cinco dias, inclusive.
- Mostrar nome da tarefa, nome do curso e data/hora do prazo; o nome da tarefa abre a atividade.
- Ordenar pelo prazo mais próximo primeiro.
- Mostrar uma mensagem simples quando não houver tarefas correspondentes.

### 3.2. Roadmap futuro (opcional)

| Versão | Funcionalidade | Por que adiar |
|---|---|---|
| V2 | Configurar a janela de dias ou incluir outros tipos de atividade. | Não é necessário para testar o MVP e aumentaria a interface e os casos de teste. |

### 3.3. Fora de escopo (todas as versões)

- Questionários e outros tipos de atividade.
- Tarefas sem prazo, tarefas já enviadas e prazos vencidos.
- E-mail, notificações push, cron e configurações administrativas.
- Adicionar automaticamente o bloco a todos os Painéis; será usado o mecanismo padrão de blocos do Moodle.

---

## 4. Requisitos Funcionais e Mecânicas

- **Cursos considerados:** cursos em que o usuário atual está matriculado e pode acessar.
- **Tarefas consideradas:** atividades visíveis do tipo Tarefa com prazo configurado.
- **Pendência:** tarefa sem envio finalizado pelo usuário atual; rascunhos não contam como tarefa concluída.
- **Janela de prazo:** prazo maior que o horário atual e menor ou igual ao horário atual mais cinco dias.
- **Exibição:** uma linha por tarefa, com nome como link, curso e prazo no fuso horário do usuário.
- **Ordenação:** prazo crescente. Se não houver resultados, exibir mensagem de lista vazia.
- **Atualização:** recalcular ao carregar o Painel; não enviar mensagens fora da página.

---

## 5. Arquitetura de Dados (Banco)

O plugin não terá tabelas próprias nem gravará dados. Ele consultará informações de matrícula,
atividades e envios que o Moodle já mantém. Portanto, não precisa de `db/install.xml`.

---

## 6. Estrutura de Diretórios e Arquivos

Legenda: **Existe** indica arquivo presente, ainda não necessariamente validado; **Criar** indica
arquivo necessário para o MVP.

```text
block_lembretes/
├── block_lembretes.php                  Existe: esqueleto do bloco
├── version.php                          Existe: metadados, requisito Moodle 4.5
├── db/upgrade.php                       Existe: rotina de upgrade
├── lang/en/block_lembretes.php          Existe: completar strings
├── lang/pt_br/block_lembretes.php       Existe: completar strings
├── classes/local/reminder_service.php   Criar: localizar tarefas pendentes
├── classes/output/renderer.php          Criar: renderizar a lista
├── classes/privacy/provider.php         Criar: declarar ausência de dados persistidos
├── templates/reminders.mustache         Criar: lista e mensagem vazia
├── tests/local/reminder_service_test.php Criar: regras de prazo, envio e ordenação
├── tests/behat/block_lembretes.feature  Criar: fluxo visível no Painel
├── README.md                            Existe
├── .gitattributes                       Existe
└── SCOPE.md                             Existe: planejamento interno
```

Não necessários no MVP: `db/install.xml`, backup/restore, Web Services, JavaScript AMD, tarefas
agendadas, eventos, configurações administrativas e armazenamento próprio.

---

## 7. Web Services (API AJAX)

Não serão usados Web Services ou AJAX no MVP. O bloco será renderizado pelo servidor quando
o estudante abrir o Painel.

---

## 8. Interface, Templates e Acessibilidade

O bloco será usado no Painel pelo mecanismo padrão de blocos do Moodle. Cada item mostrará
nome da tarefa, curso e prazo; o nome será um link para a atividade. Quando não houver tarefas,
será exibida uma mensagem simples. O HTML ficará em template Mustache e os textos virão dos
arquivos de idioma. Links devem funcionar por teclado e ter texto compreensível.

---

## 9. Internacionalização — lista inicial de strings

As mesmas chaves devem existir em inglês e português, em ordem alfabética.

| Chave | EN | PT-BR |
|---|---|---|
| `noreminders` | "No tasks due in the next 5 days." | "Você não tem tarefas com prazo nos próximos 5 dias." |
| `pluginname` | "Task reminders" | "Lembretes de tarefas" |

---

## 10. Bibliotecas de Terceiros (opcional)

> Só se o plugin empacotar bibliotecas externas. Cada uma: declarada em `thirdpartylibs.xml`,
> licença compatível com GPL v3+, `readme_moodle.txt` com URL/build/patches.
>
> **Biblioteca (JS/CSS) → empacotar no zip, NUNCA carregar de CDN.** Um **serviço** externo
> que você integra (player de vídeo, API de IA, mapas) é outra coisa — não entra aqui, vai
> para a §12 (Privacidade) via `add_external_location_link`.

| Item | Versão | Fonte | Licença | Uso |
|---|---|---|---|---|
| Nenhuma | — | — | — | O MVP usa somente recursos nativos do Moodle. |

---

## 11. Dependências e Integrações Nativas

Não há dependências de outros plugins. Serão usadas as APIs do Moodle para cursos e matrículas,
atividades do tipo Tarefa, envios e renderização de blocos.

O bloco não usa conclusão personalizada nem integração com IA.

---

## 12. Privacidade — Mapa de Dados Pessoais

> Todo plugin tem Privacy Provider. Três casos:
> - Sem dado pessoal e sem chamada externa → `\core_privacy\local\metadata\null_provider`.
> - Sem dado pessoal mas **com** chamada a serviço de terceiros → `metadata\provider` só com
>   `$collection->add_external_location_link('host', ...)`.
> - Com dado pessoal → mapear abaixo e implementar `get_metadata()`,
>   `get_contexts_for_userid()`, `export_user_data()`, `delete_data_for_user()`,
>   `delete_data_for_users()`; preferências em `export_user_preferences()`.

O plugin não guardará dados nem fará chamadas externas. Consultará dados já existentes no Moodle
para montar a lista apenas para o próprio estudante. Implementar `null_provider`.

---

## 13. Checklist de Segurança e Boas Práticas


- [ ] Resultados limitados ao usuário atual e aos cursos que ele pode acessar.
- [ ] Exibir somente atividades visíveis, com prazo futuro dentro dos cinco dias.
- [ ] Não incluir envios finalizados; escapar textos e formatar datas com APIs do Moodle.
- [ ] Evitar consultas ao banco dentro de loops (N+1).
- [ ] Manter strings em inglês e pt-BR sincronizadas; usar "estudante".
- [ ] Não criar POST, alteração de dados nem endpoints no MVP.

---

## 14. Plano de Testes

- **PHPUnit:** testar janela de cinco dias, tarefa sem prazo, tarefa vencida, envio finalizado,
  ordenação e isolamento entre estudantes.
- **Behat:** estudante vê no Painel as próprias tarefas pendentes; o link abre a atividade;
  mensagem vazia aparece quando não há resultados.
- Behat será executado pelo CI; para PHPUnit local, usar `moodle-phpunit block/lembretes`.

---

## 15. Critérios de Aceite

| ID | Critério | Status |
|---|---|---|
| CA01 | No Painel, o estudante vê somente tarefas próprias, acessíveis e não enviadas. | Pendente |
| CA02 | Só aparecem tarefas com prazo futuro até cinco dias, ordenadas pelo prazo. | Pendente |
| CA03 | Cada item mostra tarefa, curso e prazo; o link abre a tarefa correta. | Pendente |
| CA04 | Sem resultados, aparece mensagem; strings estão nos dois idiomas. | Pendente |
| CA05 | O plugin não grava dados nem envia e-mail. | Pendente |
| CA06 | Testes e verificações de estilo do plugin passam. | Pendente |

---

## 16. Roteiro de Implementação

| Fase | Trabalho | Como saber que terminou | Status |
|:--:|:--|:--|:--:|
| **1: Regras** | Implementar consulta e testes PHPUnit. | Casos de prazo, envio e ordenação passam. | Pendente |
| **2: Bloco** | Renderizar lista, mensagem vazia e strings. | Conferir a lista e os links no Painel. | Pendente |
| **3: Revisão** | Privacy Provider, Behat e verificações do plugin. | Testes passam e funcionamento é conferido no Moodle. | Pendente |

---

## 17. Decisões de Arquitetura e Trade-offs

| Decisão | Justificativa / compromisso |
|:--|:--|
| Consultar ao carregar o Painel, sem cron. | Mantém o MVP simples; a consulta deve evitar N+1 e limitar-se aos cursos acessíveis. |
| Não guardar resultados. | Evita tabela e limpeza de dados; a lista reflete o estado atual das tarefas. |
| Usar o bloco Timeline como referência. | Ele já mostra prazos; o novo bloco se justifica pelo filtro focado definido para este projeto. |

---

## 18. Histórico do Documento

| Data | Versão doc | Alteração |
|---|---|---|
| 07/10/2026 | 1.0 | Planejamento do MVP de tarefas pendentes no Painel. |
