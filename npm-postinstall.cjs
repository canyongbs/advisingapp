#!/usr/bin/env node

// This script runs after npm install

const fs = require('fs');
const path = require('path');

console.log('Running NPM Post Install...');

// Remove pusher-js integration test server package-lock.json
// This file is only used for pusher-js development and causes false
// positive security vulnerabilities in our dependency scans
const pusherLockFile = path.join(
    __dirname,
    'node_modules',
    'pusher-js',
    'integration_tests_server',
    'package-lock.json',
);
if (fs.existsSync(pusherLockFile)) {
    console.log('  Removing pusher-js integration test server package-lock.json');
    fs.unlinkSync(pusherLockFile);
}

console.log('NPM Post Install complete!');
