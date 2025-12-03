# Laravel Resume Job Search

This application is designed to help users manage their job search process. It allows users to create, update, and manage their resumes, track job applications, and organize job search activities. Built with Laravel, it leverages the framework's robust features to provide a seamless and efficient user experience.

## Features
- Resume creation and management
- Job application tracking
- Job search activity organization
- User authentication and authorization
- Integration with Ollama for enhanced resume analysis
- Utilization of LLM (Large Language Models) for AI-driven job recommendations

---

## AI Architecture: Document Parsing & RAG

This application uses a **Retrieval-Augmented Generation (RAG)** approach to analyze resumes and provide intelligent recommendations. The system extracts content from uploaded documents and augments LLM prompts with this contextual information.

### Document Parsing

The application supports multiple document formats for resume uploads:

| Format | Library | Description |
|--------|---------|-------------|
| **PDF** | [smalot/pdfparser](https://github.com/smalot/pdfparser) | Extracts text content from PDF files |
| **DOC/DOCX** | [phpoffice/phpword](https://github.com/PHPOffice/PHPWord) | Parses Microsoft Word documents |

#### How PDF Parsing Works

```
Upload → Binary Storage → On-Demand Parsing → Text Extraction → AI Analysis
```

1. **Upload**: User uploads a PDF resume through the web interface
2. **Storage**: The file is stored as binary data (`LONGBLOB`) in the database
3. **Parsing**: When viewing a resume, the PDF is parsed using `smalot/pdfparser`:
   ```php
   $parser = new Parser();
   $pdf = $parser->parseContent($binaryContent);
   $text = $pdf->getText();
   ```
4. **Text Extraction**: Plain text is extracted from the PDF structure
5. **AI Analysis**: The extracted text is sent to the LLM for analysis

#### Word Document Parsing

For `.doc` and `.docx` files, PHPWord iterates through document sections and elements:

```php
$phpWord = IOFactory::load($filePath);
foreach ($phpWord->getSections() as $section) {
    foreach ($section->getElements() as $element) {
        $text .= $element->getText();
    }
}
```

### RAG Implementation

The RAG pattern in this application works as follows:

```
┌─────────────────┐     ┌──────────────────┐     ┌─────────────────┐
│  Resume Upload  │────▶│  Text Extraction │────▶│  Store in DB    │
└─────────────────┘     └──────────────────┘     └─────────────────┘
                                                          │
                                                          ▼
┌─────────────────┐     ┌──────────────────┐     ┌─────────────────┐
│  AI Response    │◀────│  Ollama LLM      │◀────│  Build Prompt   │
└─────────────────┘     └──────────────────┘     └─────────────────┘
```

#### 1. Resume Analysis

When a resume is viewed, the system:
- Retrieves the stored document from the database
- Extracts text content using the appropriate parser
- Sends the text to Ollama with an analysis prompt
- Stores the AI analysis for future reference

```php
// OllamaService.php
public function analyzeResume($resumeText, $resume)
{
    $response = Http::post($baseUrl, [
        'model' => $llm_model,
        'prompt' => "Analyze this resume:\n\n" . $resumeText,
        'stream' => false,
    ]);
    
    return $response->json()['response'];
}
```

#### 2. Skill Extraction

The LLM extracts technical and professional skills from resume text:

```php
$prompt = "Extract technical and professional skill words from the following 
           resume text and return as comma separated array only...";
```

Extracted skills are:
- Parsed from the LLM response
- Stored in the `skills` table
- Linked to users via `user_skills` pivot table

#### 3. Job Matching (RAG Query)

The job matching feature demonstrates classic RAG by combining:
- **Retrieved Context**: The user's resume text
- **Query**: A job description
- **Augmented Prompt**: Both combined for the LLM

```php
public function matchJob($resumeText, $jobDescription)
{
    $prompt = "Compare this resume with the job description and provide 
               a match score (0-100) along with suggestions:
               
               Resume:
               $resumeText
               
               Job Description:
               $jobDescription";
    
    return $this->callOllama($prompt);
}
```

### Configuring the AI Backend

The application uses Ollama as the LLM backend. Configure in your `.env`:

```env
# Ollama Configuration
OLLAMA_API_URL=http://localhost:11434/api/generate
OLLAMA_LLM_MODEL=gemma3:4b

# For Docker environments
OLLAMA_API_URL=http://host.docker.internal:11434/api/generate
```

#### Supported Models

Any Ollama-compatible model works. Recommended options:

| Model | Size | Best For |
|-------|------|----------|
| `gemma3:4b` | 4B params | Fast, lightweight analysis |
| `llama3.2` | 3B params | Good balance of speed/quality |
| `mistral` | 7B params | Higher quality analysis |
| `llama3.1:8b` | 8B params | Best quality, slower |

#### Installing Ollama

```bash
# Linux
curl -fsSL https://ollama.com/install.sh | sh

# Pull a model
ollama pull gemma3:4b

# Start the server
ollama serve
```

---

## Installation

### Option 1: Docker Installation (Recommended)

Docker provides a consistent development environment with all dependencies pre-configured.

#### Prerequisites
- [Docker](https://docs.docker.com/get-docker/) (version 20.10+)
- [Docker Compose](https://docs.docker.com/compose/install/) (version 2.0+)

#### Quick Start

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd laravel-resume-builder-ai
   ```

2. **Copy the environment file**
   ```bash
   cp docker.env.example .env
   ```

3. **Build and start containers**
   ```bash
   docker compose build
   docker compose up -d
   ```

4. **Generate application key**
   ```bash
   docker compose exec app php artisan key:generate
   ```

5. **Run database migrations**
   ```bash
   docker compose exec app php artisan migrate
   ```

6. **Access the application**
   
   Open your browser and navigate to: `http://localhost:8080`

#### Using Make Commands

For convenience, a Makefile is provided with common commands:

```bash
# Initial setup (does steps 2-5 automatically)
make setup

# Start containers
make up

# Stop containers
make down

# View logs
make logs

# Open shell in app container
make shell

# Run artisan commands
make artisan cmd="migrate"
make artisan cmd="tinker"

# Run composer commands
make composer cmd="install"
make composer cmd="require package/name"

# Run npm commands
make npm cmd="run dev"
make npm cmd="install"

# Fresh database with seeders
make fresh

# Run tests
make test
```

#### Docker Services

The Docker setup includes the following services:

| Service | Container Name | Port | Description |
|---------|---------------|------|-------------|
| app | resume-builder-app | 9000 | PHP-FPM application |
| nginx | resume-builder-nginx | 8080 | Web server |
| db | resume-builder-db | 3306 | MySQL database |
| redis | resume-builder-redis | 6379 | Cache & queue |
| queue | resume-builder-queue | - | Queue worker |

#### Connecting to Ollama (AI Features)

If you're running Ollama on your host machine, the Docker setup is pre-configured to connect via `host.docker.internal`. Make sure Ollama is running:

```bash
# On your host machine (not in Docker)
ollama serve
```

Update your `.env` file if needed:
```env
OLLAMA_HOST=http://host.docker.internal:11434
OLLAMA_MODEL=llama3.2
```

#### Development with Hot Reloading

For frontend development with Vite hot reloading:

```bash
# Start Vite dev server inside container
docker compose exec app npm run dev
```

Or run Vite locally while the backend runs in Docker:
```bash
npm run dev
```

#### Troubleshooting Docker

**Permission issues:**
```bash
docker compose exec app chown -R laravel:laravel storage bootstrap/cache
```

**Clear all caches:**
```bash
docker compose exec app php artisan optimize:clear
```

**Rebuild containers:**
```bash
docker compose down
docker compose build --no-cache
docker compose up -d
```

**View container logs:**
```bash
docker compose logs -f app
docker compose logs -f nginx
```

---

### Option 2: Traditional PHP Installation

#### Prerequisites
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL or SQLite

#### Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd laravel-resume-builder-ai
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Set up environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   
   Edit `.env` and set your database credentials, or use SQLite:
   ```env
   DB_CONNECTION=sqlite
   ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Build assets**
   ```bash
   npm run build
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

   Or use the combined dev script:
   ```bash
   composer dev
   ```

---

## Usage
- Register or log in to your account
- Upload and manage your resume
- Track your job applications 
- Organize your job search activities
- Use AI features for resume analysis and job recommendations

## 🔮 Roadmap & Future Enhancements
* **Agentic Workflows:** Implement autonomous agents to scrape job boards based on resume keywords.
* **LinkedIn Integration:** Add one-click profile import/sync.
* **Vector Database:** Migrate from direct text analysis to a dedicated Vector DB (pgvector/ChromaDB) for semantic search across thousands of jobs.
* **Cover Letter Generator:** Context-aware generation using the analyzed resume + specific job description.

## Contributing
Feel free to submit issues or pull requests to improve the application.

## License
This project is open-source and available under the [MIT License](LICENSE).
