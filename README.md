# BookMyClass

A role-based classroom booking system with real-time availability and conflict prevention, integrated with a complete CI/CD pipeline for automated deployment.

## 🏗 Architecture
- User interacts with web application
- Code pushed to GitHub
- Jenkins triggers CI/CD pipeline
- Docker builds container image
- Image deployed to Kubernetes (Minikube)
- Application exposed via service

## ⚙️ Tech Stack
- Frontend: HTML, CSS, JS
- Backend: PHP
- Database: MySQL
- DevOps: Jenkins, Docker, Kubernetes
- Version Control: GitHub

## Setup Instructions

1. **Prerequisites**: Ensure you have a local web server environment installed (like XAMPP, WAMP, or LAMP) which includes Apache, PHP, and MySQL.
2. **Clone the repository**: Place the project files into your web server's root directory (e.g., `htdocs` for XAMPP, `www` for WAMP).
3. **Database Setup**: 
   - Create a new MySQL database named `mydb`.
   - Import the provided SQL schema (`schema.sql` or run the setup PHP scripts) to create the necessary tables.
4. **Configuration**: 
   - Update `config.php` with your database credentials if they differ from the defaults (typically `root` with a blank password for local environments).
5. **Run the Application**: 
   - Start Apache and MySQL from your server control panel.
   - Open your web browser and navigate to `http://localhost/bookmyclass`.

## Docker Run
- Start services: `docker-compose up -d --build`
- App URL: `http://localhost:8081`

## Smoke Test
- Run: `bash scripts/smoke_test.sh`
- The script checks:
  - Docker services are up
  - MySQL health is `healthy`
  - Required tables exist in `mydb`
  - Web app responds on `http://localhost:8081`

## Required Packages
The system requires standard PHP and MySQL extensions to function correctly. A list of expected server dependencies can be found in `requirements.txt`.

## Jenkins CI/CD (GitHub -> Docker Hub -> Kubernetes)

This repository now includes a `Jenkinsfile` that automates:
- Trigger on every GitHub push
- Build Docker image
- Push image to Docker Hub (`latest` + build tag)
- Apply Kubernetes manifests and roll out the new image

### 1. Jenkins Prerequisites
- Jenkins plugins:
  - Git
  - GitHub
  - Pipeline
  - Credentials Binding
- Jenkins agent/node tools:
  - `docker`
  - `kubectl`
  - access to your Kubernetes cluster

### 2. Add Jenkins Credentials
Create these credentials in Jenkins (`Manage Jenkins` -> `Credentials`):
- `dockerhub-credentials` (type: Username with password)
  - username: your Docker Hub username
  - password: Docker Hub password or access token
- `kubeconfig` (type: Secret file)
  - upload kubeconfig file for your target cluster

### 3. Create Jenkins Pipeline Job
- Use **Pipeline script from SCM**
- SCM: Git
- Repository: your GitHub repo URL
- Script Path: `Jenkinsfile`
- Optional parameters while running:
  - `DOCKERHUB_REPO` (for example `yourname/bookmyclass`)
  - `K8S_NAMESPACE` (default `default`)

### 4. Configure GitHub Webhook
In your GitHub repo:
- Go to `Settings` -> `Webhooks` -> `Add webhook`
- Payload URL: `http://<jenkins-public-url>/github-webhook/`
- Content type: `application/json`
- Event: `Just the push event`
- Save

### 5. Verify Deployment URL
After a successful pipeline run:
- Check service details:
  - `kubectl get svc bookmyclass-app-service -n <namespace> -o wide`
- With current `app-service.yaml` (`NodePort: 30081`), access:
  - `http://<node-ip>:30081`
