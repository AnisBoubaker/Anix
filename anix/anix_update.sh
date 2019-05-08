#!/bin/sh

mkdir ../tmp
cd ../tmp/
svn checkout svn://dev1.cibaxion.net/cibaxion_anixv2/trunk/anix
rm -Rf anix/custom
cp -R ../anix/custom ./anix/
rm -Rf ../anix/*
cp -R anix/* ../anix/
cd ../anix/
rm -Rf ../tmp
