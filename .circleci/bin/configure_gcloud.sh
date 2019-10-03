#!/usr/bin/env bash

set -eu

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"

SERVICE_KEY=${GCLOUD_SERVICE_KEY}
SERVICE_EMAIL=${GCLOUD_SERVICE_EMAIL}
PROJECT_ID=${GCLOUD_PROJECT}
CONFIGURATION_NAME=${GCLOUD_CONFIGURATION_NAME}

parse_args() {
    while [[ $# -gt 0 ]]; do
        case $1 in
            -k|--key) SERVICE_KEY="$2" ;;
            --email) SERVICE_EMAIL="$2" ;;
            --project) PROJECT_ID="$2" ;;
            --config-name) CONFIGURATION_NAME="$2" ;;
            *) break ;;
        esac
        shift 2
    done
    rest=("$@")
}

parse_args "$@"

echo "Update gcloud..."
sudo /opt/google-cloud-sdk/bin/gcloud --quiet components update --version 263.0.0

echo "Change permissions for gcloud..."
sudo chown -R circleci:circleci ${HOME}/.config/gcloud

echo "Decode credentials..."
echo ${SERVICE_KEY} | base64 --decode -i > ${CIRCLE_WORKING_DIRECTORY}/cloud-credentials.json

echo "Authorize service account..."
gcloud auth activate-service-account --key-file ${CIRCLE_WORKING_DIRECTORY}/cloud-credentials.json

echo "Activate configurations..."
gcloud config configurations create ${CONFIGURATION_NAME} --activate

echo "Configure account email..."
gcloud config set account ${SERVICE_EMAIL}

echo "configure compute zone..."
gcloud config set compute/zone us-central1-b

echo "Configure core/project..."
gcloud config set core/project ${PROJECT_ID}

echo "Docker login..."
docker login -u _json_key --password-stdin https://us.gcr.io < ${CIRCLE_WORKING_DIRECTORY}/cloud-credentials.json
