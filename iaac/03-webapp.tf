resource "aws_key_pair" "olw_kp" {
    key_name = "olw_kp"
    public_key = file("./.pk/olw_pk.pub")
}

data "aws_ami" "ubuntu" {}

resource "aws_instance" "olw_web" {
    count = var.SETTINGS.web_app.count
    ami = data.aws_ami.ubuntu.id
    instance_type = var.SETTINGS.web_app.instance_type
    subnet_id = aws_subnet.olw_public_subnet[count.index].id
    key_name = aws_key_pair.olw_kp.key_name
    vpc_security_group_ids = [aws_security_group.olw_web_.id]

    tags = {
        Name = "olw_web_${count.index}"
        Project = "olw"
    }
}

resource "aws_eip" "olw_web_eip" {
    count = var.SETTINGS.web_app.count
    instance = aws_instance.olw_web[count.index].id
    vpc = true

    tags = {
        Name = "olw_web_eip_${count.index}"
        Project = "olw"
    }

    connection {
        host = self.public_ip
        type = "ssh"
        user = "ubuntu"
        private_key = file("./.pk/olw_pk.pem")
    }

    provisioner "remote-exec" {
        inline = ["echo 'built server!'"]
    }

    provisioner "local-exec" {
        command = "echo ${aws_eip.olw_web_wip[count.index].public_dns} > ansible/hosts"
    }
} linha 60!