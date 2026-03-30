pipeline {
  agent any

  triggers {
    githubPush()
  }

  options {
    timestamps()
    disableConcurrentBuilds()
  }

  environment {
    GIT_REPO = 'https://github.com/chandu91822/jenkins-demo.git'
    GIT_BRANCH = 'main'
    DOCKERHUB_REPO = 'chandu91822/bookmyclass'
    IMAGE_TAG = "${BUILD_NUMBER}"
    APP_DEPLOYMENT = 'bookmyclass-app'
    APP_CONTAINER = 'bookmyclass-app'
    K8S_NAMESPACE = 'default'
  }

  stages {
    stage('Checkout') {
      steps {
        git branch: "${GIT_BRANCH}", credentialsId: 'github-cred', url: "${GIT_REPO}"
      }
    }

    stage('Prepare Image Tag') {
      steps {
        script {
          def shortCommit = sh(script: 'git rev-parse --short HEAD', returnStdout: true).trim()
          env.IMAGE_TAG = "${env.BUILD_NUMBER}-${shortCommit}"
        }
      }
    }

    stage('Build Docker Image') {
      steps {
        sh '''
          set -eu
          docker build \
            -t ${DOCKERHUB_REPO}:${IMAGE_TAG} \
            -t ${DOCKERHUB_REPO}:latest \
            .
        '''
      }
    }

    stage('Push Docker Image') {
      steps {
        withCredentials([usernamePassword(credentialsId: 'dockerhub-cred', usernameVariable: 'DOCKERHUB_USER', passwordVariable: 'DOCKERHUB_PASS')]) {
          sh '''
            set -eu
            echo "${DOCKERHUB_PASS}" | docker login -u "${DOCKERHUB_USER}" --password-stdin
            docker push ${DOCKERHUB_REPO}:${IMAGE_TAG}
            docker push ${DOCKERHUB_REPO}:latest
            docker logout
          '''
        }
      }
    }

    stage('Deploy To Kubernetes') {
      steps {
        withCredentials([file(credentialsId: 'kubeconfig', variable: 'KUBECONFIG_FILE')]) {
          sh '''
            set -eu
	    export KUBECONFIG=$HOME/.kube/config           	
            kubectl apply -n ${K8S_NAMESPACE} -f mysql-service.yaml
            kubectl apply -n ${K8S_NAMESPACE} -f mysql-deployment.yaml
            kubectl apply -n ${K8S_NAMESPACE} -f app-service.yaml
            kubectl apply -n ${K8S_NAMESPACE} -f app-deployment.yaml

            kubectl set image deployment/${APP_DEPLOYMENT} ${APP_CONTAINER}=${DOCKERHUB_REPO}:${IMAGE_TAG} -n ${K8S_NAMESPACE}
            kubectl rollout status deployment/${APP_DEPLOYMENT} -n ${K8S_NAMESPACE} --timeout=240s

            kubectl get svc bookmyclass-app-service -n ${K8S_NAMESPACE} -o wide
          '''
        }
      }
    }
  }

  post {
    always {
      sh '''
        docker image prune -f || true
      '''
    }
  }
}
