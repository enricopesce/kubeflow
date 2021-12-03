This project is a lab for understanding how I can setup a complete workflow from development to production with some objectives:

1) minor number of tools
2) better development experience
3) identical infrastructure at every stage
4) continuos deployment

## Install Kubernetes and friends (for mac users)

To start you need to prepare a local kubernetes setup with Minikube and Devspace:

    brew install minikube hyperkit devspace
    minikube start --driver=hyperkit
    minikube addons enable metrics-server
    minikube addons enable ingress

To deep dive to Devspace follow this official onboarding page https://devspace.sh/cli/docs/guides/basics

## Starts development mode

DevSpace allows you to develop applications directly inside a Kubernetes cluster.
The biggest advantages of developing directly inside Kubernetes is that your dev environment will be very similar to your release (production\staging etc) environment and you can have much greater confidence that everything will work in the same environment when shipping new features.

    devspace use namespace kubeflow-dev
    devspace dev

Now you can launch commands inside the pod es:

    composer require --dev phpunit/phpunit
    ...
    ...

an you can develop with your preferred IDE.

## Tests with non development without CD

If you need to test K8s modifications or new features you can work with the development mode or you can launch the release environment locally or remotely without ArgoCD or CI\CD and a git trigger.

    devspace use profile release
    devspace use namespace kubeflow-relese
    devspace deploy

## Cleanup dev resources

    devspace purge

### ArgoCD setup

In your K8s cluster install ArgoCD:

    kubectl create namespace argocd
    kubectl apply -n argocd -f https://raw.githubusercontent.com/argoproj/argo-cd/stable/manifests/install.yaml
    kubectl port-forward svc/argocd-server -n argocd 8090:443

Get the password admin

    kubectl -n argocd get secret argocd-initial-admin-secret -o jsonpath="{.data.password}" | base64 -d

Now you can test and monitor the deployments with http://localhost:8090

### ArgoCD adding application deployment

Define the ArgoCD application, the production environment is based on the branch:

    kubectl apply -n argocd -f infrastructure/argocd/prd.yaml

also if you need staging:

    kubectl apply -n argocd -f infrastructure/argocd/stg.yaml

## Cleanup all

    minikube delete