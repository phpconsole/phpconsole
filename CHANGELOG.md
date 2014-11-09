# Changelog

## v3.2.0
- Removed Guzzle from dependencies
- Lowered required PHP version to 5.3.0
- Resurrected p() function
- Simplified standalone package

## v3.1.1
- Added more config locations

## v3.1.0
- Introduced debug mode

## v3.0.3
- Fixed static class P

## v3.0.2
- Added more config locations to check

## v3.0.1
- Switched back from Guzzle 4 to Guzzle 3

## v3.0.0
- Code overhaul
- PSR2 compatibility
- Added option to encrypt data with AES-256
- Saving hostname
- Added option to switch between print_r() and var_dump()

## v2.0.1
- Fixed mistyped function name

## v2.0.0
- Code converted into Composer package
- Added option to set "type" of snippet
- Fixed double port number in address

## v1.6.0
- Got rid of user API key
- Send data to a first specified user if no user's nickname supplied and auto recognition is off

## v1.5.2
- Fixed bug when iterating on objects with iterator interface

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
