The following is the process to package a release.

  * Double check the release label in the build.xml file
    * Version label
    * Release label
  * Export the development trunk of the versioning tree, run the following:
    * `$ svn export https://rati.googlecode.com/svn/trunk/ rati_trunk_export`
  * Set any passwords needed for non-development environments
  * Build package by running `ant`
  * See results of build, if successful, see dist/ directory for archives (should be zip and tar.gz release)