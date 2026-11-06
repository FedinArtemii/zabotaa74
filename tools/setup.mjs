import fs from 'node:fs';
import path from 'node:path';
import crypto from 'node:crypto';
import { execFileSync } from 'node:child_process';
const root = process.cwd();
const local = path.join(root, '.local');
const wp = path.join(local, 'wordpress');
fs.mkdirSync(path.join(local, 'downloads'), {recursive: true});
for (const [url, name] of [
  ['https://wordpress.org/latest.zip', 'wordpress.zip'],
  ['https://downloads.wordpress.org/plugin/sqlite-database-integration.latest-stable.zip', 'sqlite.zip'],
  ['https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar', 'wp-cli.phar']
]) {
  const dest = path.join(local, 'downloads', name);
  if (!fs.existsSync(dest)) {
    const response = await fetch(url);
    if (!response.ok) throw Error(`Download failed: ${url} (${response.status})`);
    fs.writeFileSync(dest, Buffer.from(await response.arrayBuffer()));
  }
}
function unzip(file, destination) {
  if (process.platform === 'win32') {
    execFileSync('powershell.exe', ['-NoProfile', '-Command', '& { param($archive,$destination) Expand-Archive -LiteralPath $archive -DestinationPath $destination -Force }', file, destination], {stdio: 'inherit'});
  } else execFileSync('unzip', ['-oq', file, '-d', destination], {stdio: 'inherit'});
}
if (!fs.existsSync(path.join(wp, 'wp-load.php'))) unzip(path.join(local, 'downloads/wordpress.zip'), local);
const plugin = path.join(wp, 'wp-content/plugins/sqlite-database-integration');
if (!fs.existsSync(plugin)) {
  unzip(path.join(local, 'downloads/sqlite.zip'), path.join(local, 'downloads/sqlite'));
  fs.cpSync(path.join(local, 'downloads/sqlite/sqlite-database-integration'), plugin, {recursive: true});
}
fs.copyFileSync(path.join(plugin, 'db.copy'), path.join(wp, 'wp-content/db.php'));
fs.cpSync(path.join(root, 'theme/zabota74'), path.join(wp, 'wp-content/themes/zabota74'), {recursive: true});
const phpDirectory = process.platform === 'win32' ? 'C:/php' : null;
const ini = path.join(local, 'php.ini');
if (!fs.existsSync(ini)) {
  fs.writeFileSync(ini, [
    ...(phpDirectory ? [`extension_dir="${phpDirectory}/ext"`] : []),
    'extension=pdo_sqlite', 'extension=sqlite3', 'extension=mbstring', 'extension=openssl',
    'extension=fileinfo', 'memory_limit=256M', 'max_execution_time=120', 'upload_max_filesize=32M',
    'post_max_size=32M', 'date.timezone=Asia/Yekaterinburg', 'display_errors=Off', 'log_errors=On',
    `error_log="${path.join(local, 'php-errors.log').replaceAll('\\', '/')}"`
  ].join('\n'));
}
const config = path.join(wp, 'wp-config.php');
if (!fs.existsSync(config)) {
  const salts = ['AUTH_KEY','SECURE_AUTH_KEY','LOGGED_IN_KEY','NONCE_KEY','AUTH_SALT','SECURE_AUTH_SALT','LOGGED_IN_SALT','NONCE_SALT'].map(key => `define('${key}', '${crypto.randomBytes(48).toString('hex')}');`).join('\n');
  fs.writeFileSync(config, `<?php\ndefine('DB_NAME','zabota74');\ndefine('DB_USER','local');\ndefine('DB_PASSWORD','');\ndefine('DB_HOST','localhost');\ndefine('DB_CHARSET','utf8mb4');\ndefine('DB_COLLATE','');\ndefine('WP_HOME','http://127.0.0.1:8074');\ndefine('WP_SITEURL',WP_HOME);\ndefine('WP_ENVIRONMENT_TYPE','local');\ndefine('DISALLOW_FILE_EDIT',true);\ndefine('WP_DEBUG',false);\ndefine('WP_AUTO_UPDATE_CORE',false);\n${salts}\n$table_prefix='zb_';\nif(!defined('ABSPATH')) define('ABSPATH',__DIR__.'/');\nrequire_once ABSPATH.'wp-settings.php';\n`);
}
let currentConfig = fs.readFileSync(config, 'utf8');
if (!currentConfig.includes("DISABLE_WP_CRON")) {
  currentConfig = currentConfig.replace("define('WP_DEBUG',false);", "define('WP_DEBUG',false);\ndefine('DISABLE_WP_CRON',true);\ndefine('AUTOMATIC_UPDATER_DISABLED',true);");
  fs.writeFileSync(config, currentConfig);
}
const php = process.env.PHP_BIN || 'php';
const cli = (...args) => execFileSync(php, ['-c', ini, path.join(local, 'downloads/wp-cli.phar'), `--path=${wp}`, ...args], {stdio: 'inherit', env: {...process.env, WP_CLI_PHP: php}});
let installed = true;
try { execFileSync(php, ['-c', ini, path.join(local, 'downloads/wp-cli.phar'), `--path=${wp}`, 'core', 'is-installed'], {stdio: 'pipe'}); } catch { installed = false; }
if (!installed) {
  const password = crypto.randomBytes(18).toString('base64url');
  const admin = `zabota_${crypto.randomBytes(4).toString('hex')}`;
  const child = execFileSync(php, ['-c', ini, path.join(local, 'downloads/wp-cli.phar'), `--path=${wp}`, 'core', 'install', '--url=http://127.0.0.1:8074', '--title=Забота — пансионат для пожилых', `--admin_user=${admin}`, '--admin_email=local@example.test', '--skip-email', '--prompt=admin_password'], {input: password+'\n', encoding: 'utf8'});
  fs.writeFileSync(path.join(local, 'admin-credentials.txt'), `Local WordPress only\nURL: http://127.0.0.1:8074/wp-admin/\nUsername: ${admin}\nPassword: ${password}\n`, {mode: 0o600});
  console.log(child.replaceAll(password, '[hidden]'));
}
cli('theme', 'activate', 'zabota74');
if (!fs.existsSync(path.join(wp,'wp-content/languages/ru_RU.mo'))) cli('language','core','install','ru_RU','--activate');
cli('eval-file', path.join(root, 'tools/seed.php'));
console.log('WordPress is ready. npm start → http://127.0.0.1:8074');
