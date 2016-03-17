Barcamp Salzburg 2016
=====================

This repo contains a Vagrant virtual machine and workshop files for the Barcamp
Salzburg 2016. https://barcamp-sbg.at/

## Prerequisites

You need to have following installed:
- [Vagrant]
- [VirtualBox]

The minimum recommended version of vagrant at the time of writing is 1.8.1

With these versions you can use only Virtualbox 5.x

You will also need to have hardware virtualization option activated in bios, if
you have one.

## Workshop Virtual Machine Setup

The local development is meant to be used in a vagrant provisioned box.

The provisioner for the project is Puppet.

Once you have the prerequisites setup, clone the repo, and from the cloned repo
directory run the
```
vagrant up
```

from you terminal to start the process up.

If you do not see an error message, go get yourself a cup of coffee or your
favorite beverage, you deserve it.

If you start seeing the connection timeout after adding of the private key
```
    default: SSH username: vagrant
    default: SSH auth method: private key
    default: Warning: Connection timeout. Retrying...
    default: Warning: Connection timeout. Retrying...
```
You should open up the Virtualbox, click the vm running (name should be along
the lines of barcamp-sbg-2016....) and reset it (on OSX it is cmd+t). This is
due to some weird bug somewhere on intersection of vagrant, virtualbox and this
ubuntu cloud image. After the initial virtual machine build, you will not need
to use this.

If you experience error along the lines of
```
==> default: Adding box '{{ boxname }}' ({{ boxversion}}) for provider: virtualbox
    default: Downloading: https://atlas.hashicorp.com/{{ boxurl }}.box
==> default: Box download is resuming from prior download progress
An error occurred while downloading the remote file. The error
message, if any, is reproduced below. Please fix this error and try
again.

HTTP server doesn't seem to support byte ranges. Cannot resume.
```

you will need to execute
```
rm ~/.vagrant.d/tmp/*
```

You may be required to use admin privileges to execute this.

If for any reason you need to reprovision the vm, you will need to run

```
vagrant provision
```

Be careful with this one, as it takes a LOT of time on slow connection.

***IMPORTANT***

If vagrant starts complaining about locale and crashes the provisioning, in
~/.bash_profile (or equivalent) add

```
export LC_ALL=en_US.UTF-8
export LANG=en_US.UTF-8
```

## Hosts Setup

You will need to add the following to your hosts file

```
10.105.82.16 symfony.dev
```

Linux/MacOS systems location of the hosts file is
```
/etc/hosts
```

Location on Windows systems is along the lines of
```
C:\Windows\System32\Drivers\etc\hosts
```

You may be required to use admin privileges to edit the hosts file.

## Credits

The vagrant installation is created with the awesome [PuPHPet] tool. If you ever
need a custom vagrant box quickly, PuPHPet is the way to go :)

The idea of the vagrant with workshops git is proudly copied and adapted from
the [PHP Summercamp 2015] :D

[Vagrant]: http://www.vagrantup.com/downloads.html
[VirtualBox]: https://www.virtualbox.org/wiki/Downloads
[PuPHPet]: https://puphpet.com/
[PHP Summercamp 2015]: https://github.com/netgen/summercamp-2015
