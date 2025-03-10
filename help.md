commands for k8s deploy - 

kubectl create ns aura

kubectl create deployment aura --image=padminisys/aura -n aura

kubectl expose deployment/aura --port=80 -n aura

kubectl create ingress -n aura aura-ingress --class=nginx --rule="auraadventurejunction.in/*=aura:80,tls=aura-tls-secret" --annotation cert-manager.io/cluster-issuer=letsencrypt-prod

below is important command to run in linux - 
base64 -w 0 < ~/.kube/config

