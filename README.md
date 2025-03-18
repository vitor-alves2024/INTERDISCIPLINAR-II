# Interdiciplinar-II
# Agenda Inteligente com IA - Reconhecimento de Áudio e Alarmes Automáticos

## Descrição

A **Agenda Inteligente com IA** é um aplicativo que permite ao usuário adicionar compromissos e tarefas simplesmente enviando áudios. O sistema usa **reconhecimento de voz** para transcrever o áudio em texto e **Processamento de Linguagem Natural (NLP)** para identificar as informações da tarefa (como data e hora), criando automaticamente eventos com **alarmes** ou **notificações** para o usuário.

---

## Funcionalidades

- **Transcrição de Áudio para Texto**:
  O usuário envia um áudio (ex: "Me lembre de ligar para o João amanhã às 15h"). A IA transcreve o áudio em texto e analisa o conteúdo para identificar a tarefa e o horário.

- **Criação Automática de Tarefas e Alarmes**:
  Com base na transcrição, o sistema cria automaticamente a tarefa/evento e configura um **alarme** ou **notificação** no horário especificado.

- **Reconhecimento de Comandos Orais**:
  O usuário pode dar comandos simples como "adicionar evento", "criar lembrete" e a IA irá processá-los automaticamente.

- **Integração com Calendários**:
  Sincronização com calendários populares como **Google Calendar** ou **Apple Calendar** para importar/exportar eventos.

- **Notificações de Lembretes**:
  O sistema envia **notificações** para alertar o usuário sobre seus compromissos.

---

## Tecnologias Utilizadas

- **Reconhecimento de Voz**:
  - **Google Cloud Speech-to-Text** ou **Microsoft Azure Speech Services**.

- **Processamento de Linguagem Natural (NLP)**:
  - **spaCy** ou **Dialogflow** para extrair datas, horários e tarefas.

- **Backend**:
  - **Python** (Flask ou Django) ou **Node.js**.

- **Notificações e Alarmes**:
  - **Firebase Cloud Messaging (FCM)** para notificações.

- **Integração com Calendário**:
  - **Google Calendar API** ou **Apple Calendar API**.

- **Frontend**:
  - **Flutter** (para app móvel) ou **React.js** (para web).

---

## Como Funciona

1. O usuário envia um áudio com a descrição de uma tarefa ou compromisso.
2. O sistema converte o áudio em texto usando **Speech-to-Text**.
3. A IA analisa o texto para identificar a tarefa, data e hora usando **NLP**.
4. O sistema cria o evento ou lembrete e configura um alarme/notificação no horário desejado.
5. O usuário recebe uma notificação lembrando-o da tarefa no horário determinado.

---

## Como Executar Localmente

### Pré-requisitos:

- **Node.js** (para frontend se for usar React.js)
- **Flutter** (para app móvel)
- **Python 3.x** (para o backend)
- **Conta no Google Cloud** (para APIs de Speech-to-Text e Calendar)

### Passos para Executar:

1. Clone o repositório:
   ```bash
   git clone https://github.com/seu-usuario/agenda-inteligente-com-ia.git
