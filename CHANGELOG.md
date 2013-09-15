# Changelog

## v1.5.1
- Switched from serialize() to print_r()
- Replacing true/false/null before sending data

## v1.5
- Switched back from json_encode to serialize()
- More context sent by default
- Bundled cacert.pem file
- Removed counter functionality

## v1.4.1
- Fixed backtrace_depth when sending data to all users

## v1.4
- Added context functionality
- Switched from serialize() to json_encode() for data sent

## v1.3
- Added sending data to all users when using nickname 'all'

## v1.2.1
- Fixed SSL test throwing error 400

## v1.2
- Added SSL test

## v1.1.4
- Added option for path to external certificates

## v1.1.3
- Updated current page address function

## v1.1.2
- Fixed shutdown function

## v1.1.1
- Updated API address to https://

## v1.1
- Code converted into OO library + wrapper
- Fixed counter
- Fixed destroying cookies
- Added cURL error reporting
- Added backtrace_depth setting

## v1.0
- Initial release
