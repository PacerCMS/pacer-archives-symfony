pipeline {
  agent any
  stages {
    stage('Build') {
      steps {
        slackSend (message: "${currentBuild.fullDisplayName} Build started (<${env.BUILD_URL}|Open>)", color: '#37b787')
        sh 'bundle install'
        sh 'composer install'
        sh 'npm ci'
        sh 'npm run build'
      }
      post {
        success {
          slackSend (message: "${currentBuild.fullDisplayName} Success after ${currentBuild.durationString.minus(' and counting')} (<${env.BUILD_URL}|Open>)", color: '#37b787')
        }
        failure {
          slackSend (message: "${currentBuild.fullDisplayName} Failed after ${currentBuild.durationString.minus(' and counting')} (<${env.BUILD_URL}|Open>)", color: '#ff0000')
        }
      }
    }
    stage('Test') {
      environment {
        APP_ENV = 'test'
        SYMFONY_PHPUNIT_DIR = './bin/.phpunit'
        SYMFONY_DEPRECATIONS_HELPER = 'disabled'
      }
      steps {
        slackSend (message: "${currentBuild.fullDisplayName} Test started (<${env.BUILD_URL}|Open>)", color: '#37b787')
        sh './bin/console doctrine:database:drop --force'
        sh './bin/console doctrine:database:create'
        sh './bin/console doctrine:migrations:migrate -n'
        sh './bin/console doctrine:fixtures:load -n'
        sh './bin/phpunit'
      }
      post {
        success {
          slackSend (message: "${currentBuild.fullDisplayName} Success after ${currentBuild.durationString.minus(' and counting')} (<${env.BUILD_URL}|Open>)", color: '#37b787')
        }
        failure {
          slackSend (message: "${currentBuild.fullDisplayName} Failed after ${currentBuild.durationString.minus(' and counting')} (<${env.BUILD_URL}|Open>)", color: '#ff0000')
        }
      }
    }
    stage('Deploy to Staging') {
      when {
        expression {
          isMaster = env.BRANCH_NAME == "master"
          isPrBranch = env.BRANCH_NAME ==~ /PR\-[0-9]+/
          return !isMaster && !isPrBranch
        }
      }
      steps {
        slackSend (message: "${currentBuild.fullDisplayName} Deploy to Staging started (<${env.BUILD_URL}|Open>)", color: '#37b787')
        sh 'bundle install'
        sh "bundle exec cap staging deploy BRANCH=${env.GIT_BRANCH}"
      }
      post {
        success {
          slackSend (message: "${currentBuild.fullDisplayName} Success after ${currentBuild.durationString.minus(' and counting')} (<${env.BUILD_URL}|Open>)", color: '#37b787')
        }
        failure {
          slackSend (message: "${currentBuild.fullDisplayName} Failed after ${currentBuild.durationString.minus(' and counting')} (<${env.BUILD_URL}|Open>)", color: '#ff0000')
        }
      }
    }
    stage('Deploy to Production') {
      when {
        branch 'master'
      }
      steps {
        slackSend (message: "${currentBuild.fullDisplayName} Deploy to Production started (<${env.BUILD_URL}|Open>)", color: '#37b787')
        sh 'bundle install'
        sh "bundle exec cap production deploy BRANCH=${env.GIT_BRANCH}"
      }
      post {
        success {
          slackSend (message: "${currentBuild.fullDisplayName} Success after ${currentBuild.durationString.minus(' and counting')} (<${env.BUILD_URL}|Open>)", color: '#37b787')
        }
        failure {
          slackSend (message: "${currentBuild.fullDisplayName} Failed after ${currentBuild.durationString.minus(' and counting')} (<${env.BUILD_URL}|Open>)", color: '#ff0000')
        }
      }
    }
  }
}
