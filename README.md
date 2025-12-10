# Laravel Resume Job Search

This application is designed to help users manage their job search process. It allows users to create, update, and manage their resumes, track job applications, and organize job search activities. Built with Laravel, it leverages the framework's robust features to provide a seamless and efficient user experience.

## Features
- Resume creation and management
- Job application tracking
- Job search activity organization
- User authentication and authorization
- **Admin Panel**: Comprehensive admin dashboard for managing users, jobs, and applications
- **Job Posting**: Admin-only job posting with full CRUD operations
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
# Ollama Configuration (local install)
OLLAMA_API_URL=http://localhost:11434/api/generate
OLLAMA_LLM_MODEL=gemma3:4b

# For Docker environments, use host.docker.internal to reach the host machine:
# OLLAMA_API_URL=http://host.docker.internal:11434/api/generate
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
OLLAMA_API_URL=http://host.docker.internal:11434/api/generate
OLLAMA_LLM_MODEL=llama3.2
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

## Admin Features

The application includes a comprehensive admin panel accessible only to users with the `admin` role.

### Admin Dashboard

Access the admin dashboard at `/admin/dashboard` (requires admin role). The dashboard provides:

- **Statistics Overview**: Total users, jobs, applications, and resumes
- **Recent Activity**: Latest users, jobs, and applications
- **Quick Actions**: Direct links to manage users, jobs, and applications

### Admin Capabilities

Admins have access to:

1. **User Management** (`/admin/users`)
   - View all registered users
   - See user statistics (resumes, applications)
   - View user roles

2. **Job Management** (`/admin/jobs`)
   - View all job listings
   - Edit or delete any job
   - See application counts per job

3. **Application Management** (`/admin/applications`)
   - View all job applications
   - See applicant and job details
   - Track application statuses

4. **Job Posting**
   - Only admins can create, edit, or delete job listings
   - Regular users can view and apply to jobs but cannot post them

### Default Admin User

After running seeders, an admin user is created:

- **Email**: `admin@example.com`
- **Password**: `admin123`
- **Role**: `admin`

**Important**: Change the default admin password after first login!

### Admin Middleware

Admin routes are protected by the `EnsureUserIsAdmin` middleware, which checks if the authenticated user has the `admin` role. Non-admin users attempting to access admin routes will receive a 403 Forbidden error.

## Database Seeding

To populate the database with sample data for testing:

```bash
# Using Docker
docker compose exec app php artisan db:seed

# Or locally
php artisan db:seed
```

To reset and seed the database:

```bash
# Using Docker
docker compose exec app php artisan migrate:fresh --seed

# Or locally
php artisan migrate:fresh --seed
```

Available seeders:
- `SkillSeeder` - Populates skills table with technical and professional skills
- `PromptSeeder` - Seeds baseline LLM prompts and Ollama configs

### Admin: LLM Prompts
- Manage prompts and Ollama settings at `/admin/prompts` (admin only).
- Fields:
  - `key` (unique), `title`, `body` (supports placeholders like `{{resume_text}}`, `{{job_description}}`, `{{applicant_name}}`).
  - Ollama config per prompt: `temperature`, `top_p`, `top_k`, `repeat_penalty`, `num_ctx`, `seed`, `max_tokens`.
- Prompts are pulled from the `prompts` table for resume analysis, skill extraction, job matching, and cover letters. If a DB prompt is missing, built-in defaults are used.
- `UserSeeder` - Creates test users (including admin user)
- `UserDetailSeeder` - Creates user profile details
- `JobSeeder` - Creates 15 demo job listings with realistic data

### Seeded Data Details

**Users:**
- Test user: `test@home.net` / `password` (role: job_seeker)
- Admin user: `admin@example.com` / `admin123` (role: admin)
- 10 additional factory-generated users (role: job_seeker)

**Jobs:**
- 15 diverse job listings across 5 companies
- Job titles include: Senior Laravel Developer, Full Stack Developer, DevOps Engineer, Data Scientist, Cybersecurity Specialist, and more
- Multiple locations: San Francisco, New York, Remote, Austin, Boston, etc.
- Realistic job descriptions and requirements
- Associated companies: TechCorp Solutions, Digital Innovations Inc, Cloud Systems Ltd, Data Analytics Pro, SecureNet Technologies

---

## Testing

The project includes comprehensive test coverage. Run tests with:

```bash
# Using Docker
docker compose exec app php artisan test

# Or using Make
make test

# Or locally
php artisan test
```

### Test Coverage

- **Authentication Tests**: Registration, login, logout, password reset (7 tests)
- **Resume Tests**: Upload (PDF/DOCX), download, view, AI analysis (13 tests)
- **Admin Tests**: Admin dashboard, user management, job management, authorization (21 tests)
- **Job Application Tests**: CRUD operations, authorization (9 tests)
- **Profile Tests**: User details, skills management (5 tests)
- **AI Service Tests**: Resume analysis, skill extraction, job matching (6 tests)
- **Model Relationship Tests**: All model relationships (8 tests)
- **UI Tests**: Authentication UI, form accessibility, Bootstrap styling (20 tests)

**Total: 110 tests with 370 assertions**
- **Profile & Skills Tests**: Update details, add/remove skills
- **AI Service Tests**: Resume analysis, skill extraction, job matching (mocked)
- **Job & Application Tests**: CRUD operations, application tracking
- **Model Relationship Tests**: All Eloquent relationships

### Running Specific Tests

```bash
# Run a specific test file
php artisan test --filter ResumeTest

# Run with coverage (requires Xdebug)
php artisan test --coverage
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
