sudo dnf remove docker \
                  docker-client \
                  docker-client-latest \
                  docker-common \
                  docker-latest \
                  docker-latest-logrotate \
                  docker-logrotate \
                  docker-selinux \
                  docker-engine-selinux \
                  docker-engine
sudo dnf -y install dnf-plugins-core
sudo dnf config-manager --add-repo https://download.docker.com/linux/fedora/docker-ce.repo
sudo dnf install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
sudo systemctl enable --now docker
sudo docker run hello-world
sudo docker ps
sudo usermod -aG docker $USER
sudo cat > /etc/docker/daemon.json << EOF
{
"registry-mirrors": [
"https://docker.mirrors.ustc.edu.cn",
"https://hub-mirror.c.163.com",
"https://reg-mirror.qiniu.com",
"https://registry.docker-cn.com"
],
"exec-opts": ["native.cgroupdriver=systemd"]
}
EOF
sudo systemctl daemon-reload
sudo systemctl restart docker
sudo systemctl status docker
sudo dnf install -y git make iproute-tc
sudo wget https://go.dev/dl/go1.20.5.linux-amd64.tar.gz
sudo rm -rf /usr/local/go && tar -C /usr/local -xzf go1.20.5.linux-amd64.tar.gz
echo 'export PATH=$PATH:/usr/local/go/bin' >> /etc/profile
source /etc/profile
go version
git clone https://github.com/Mirantis/cri-dockerd.git
cd cri-dockerd
make cri-dockerd
mkdir -p /usr/local/bin
install -o root -g root -m 0755 cri-dockerd /usr/local/bin/cri-dockerd
install packaging/systemd/* /etc/systemd/system
sed -i -e 's,/usr/bin/cri-dockerd,/usr/local/bin/cri-dockerd,' /etc/systemd/system/cri-docker.service
systemctl daemon-reload
systemctl enable cri-docker.service
systemctl enable --now cri-docker.socket
systemctl status cri-docker.service
cat <<EOF | sudo tee /etc/yum.repos.d/kubernetes.repo
[kubernetes]
name=Kubernetes
baseurl=https://packages.cloud.google.com/yum/repos/kubernetes-el7-\$basearch
enabled=1
gpgcheck=1
gpgkey=https://packages.cloud.google.com/yum/doc/rpm-package-key.gpg
exclude=kubelet kubeadm kubectl
EOF
sudo setenforce 0
sudo sed -i 's/^SELINUX=enforcing$/SELINUX=permissive/' /etc/selinux/config
sudo yum install -y kubelet-1.27.2 kubeadm-1.27.2 kubectl-1.27.2 --disableexcludes=kubernetes
sudo systemctl enable --now kubelet
sudo firewall-cmd --add-port=6443/tcp --permanent
sudo firewall-cmd --add-port=10250/tcp --permanent
sudo firewall-cmd --reload
sudo dnf remove -y zram-generator-defaults
sudo swapoff -a
sudo swapon --show
sudo cat <<EOF | sudo tee /etc/modules-load.d/k8s.conf
overlay
br_netfilter
EOF
sudo modprobe overlay
sudo modprobe br_netfilter
sudo cat <<EOF | sudo tee /etc/sysctl.d/k8s.conf
net.bridge.bridge-nf-call-iptables  = 1
net.bridge.bridge-nf-call-ip6tables = 1
net.ipv4.ip_forward                 = 1
EOF
sudo sysctl --system
lsmod | grep br_netfilter
lsmod | grep overlay
sysctl net.bridge.bridge-nf-call-iptables net.bridge.bridge-nf-call-ip6tables net.ipv4.ip_forward
echo 'apiVersion: kubeadm.k8s.io/v1beta3
kind: InitConfiguration
bootstrapTokens:
  - token: "9a08jv.c0izixklcxtmnze7"
    description: "kubeadm bootstrap token"
    ttl: "24h"
    usages:
      - authentication
      - signing
    groups:
      - system:bootstrappers:kubeadm:default-node-token
nodeRegistration:
  name: "Master"
  criSocket: "/var/run/cri-dockerd.sock"
  taints: null
  imagePullPolicy: "IfNotPresent"
localAPIEndpoint:
  advertiseAddress: "192.168.100.45"
  bindPort: 6443
---
apiServer:
  timeoutForControlPlane: 4m0s
apiVersion: kubeadm.k8s.io/v1beta3
certificatesDir: "/etc/kubernetes/pki"
clusterName: "kubernetes"
controllerManager: {}
dns: {}
etcd:
  local:
    dataDir: /var/lib/etcd
    imageRepository: registry.aliyuncs.com/google_containers
kind: ClusterConfiguration
kubernetesVersion: 1.27.2
networking:
  dnsDomain: "cluster.local"
  serviceSubnet: "192.168.100.0/24"
  podSubnet: "172.17.0.0/16"
scheduler: {}
---
kind: KubeletConfiguration
apiVersion: kubelet.config.k8s.io/v1beta1
cgroupDriver: systemd   # Set it to systemd'  > kubeadm-config.yaml
sudo kubeadm init --config kubeadm-config.yaml --v=5
mkdir -p $HOME/.kube
sudo cp -i /etc/kubernetes/admin.conf $HOME/.kube/config
sudo chown $(id -u):$(id -g) $HOME/.kube/config
echo 'export KUBECONFIG=/etc/kubernetes/admin.conf' >> /etc/profile
kubeadm join 192.168.100.45:6443 --token <token> \
--discovery-token-ca-cert-hash sha256:<cert_master_server> \
--cri-socket unix:///var/run/cri-dockerd.sock \
--node-name <nodename>
echo 'export KUBECONFIG=/etc/kubernetes/kubelet.conf' >> /etc/profile
unset http_proxy
unset https_proxy
sudo kubectl create -f https://raw.githubusercontent.com/projectcalico/calico/v3.26.1/manifests/custom-resources.yaml
sudo wget https://raw.githubusercontent.com/projectcalico/calico/v3.26.1/manifests/custom-resources.yaml
sudo watch kubectl get pods -n calico-system
sudo kubectl get nodes -o wide
sudo docker build -t server-apache-php:v1.0.0 .
sudo docker run --rm -p 8084:80 server-apache-php:v1.0.0
sudo docker save server-apache-php:v1.0.0 > /root/server-apache-php.tar
sudo scp server-apache-php.tar root@192.168.100.46:/root/server-apache-php.tar
sudo scp server-apache-php.tar root@192.168.100.47:/root/server-apache-php.tar
sudo docker load -i /root/server-apache-php.tar
sudo kubectl apply -f podtest.yaml
sudo watch kubectl get pods
sudo kubectl port-forward --address 192.168.100.45 deployment.apps/server-api-deployment 8084:80
