Barcamp Salzburg 2016
=====================

This repo contains a Vagrant virtual machine and workshop files for the Barcamp
Salzburg 2016. https://barcamp-sbg.at/

## Prerequisites

You need to have following installed:
- [Vagrant]
- [VirtualBox]

The minimum required version of vagrant at the time of writing is 1.8.1

The minimum required version of Virtualbox is a 5.x one.

You will also need to have hardware virtualization option activated in bios, if
you have one.

## Workshop Virtual Machine Setup

The local development is meant to be used in a vagrant provisioned box.

The provisioner for the project is Puppet, but you get an already provisioned box.

Once you have the prerequisites setup, clone the repo, and from the cloned repo
directory run the
```
vagrant up
```

from you terminal to start the process up.

If you do not see an error message, go get yourself a cup of coffee or your
favorite beverage, you deserve it.

If you experience error along the lines of
```
==> barcamp-sbg-2016: Adding box 'pixelart/barcamp-sbg-2016' (v1.0.0) for provider: virtualbox
    barcamp-sbg-2016: Downloading: https://atlas.hashicorp.com/pixelart/boxes/barcamp-sbg-2016/versions/1.0.0/providers/virtualbox.box
==> barcamp-sbg-2016: Box download is resuming from prior download progress
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
