// Full-page 1280px screenshots via the Chrome DevTools Protocol (Node 22+, google-chrome on PATH).
// Usage (app running on :8000):
//   node docs/screenshots/capture.mjs docs/screenshots/city-austin.app.png http://127.0.0.1:8000/texas/austin \
//        docs/screenshots/city-austin.design.png "file://$PWD/designs/city-austin.html"
import { spawn } from 'node:child_process';
import { writeFileSync, mkdtempSync } from 'node:fs';
import { tmpdir } from 'node:os';
const port = 9333;
const chrome = spawn('google-chrome', ['--headless=new', '--no-sandbox', '--disable-gpu', '--hide-scrollbars',
  `--remote-debugging-port=${port}`, `--user-data-dir=${mkdtempSync(tmpdir() + '/cdp-')}`, '--window-size=1280,900', 'about:blank'], { stdio: 'ignore' });
const sleep = (ms) => new Promise((r) => setTimeout(r, ms));
let target;
for (let i = 0; i < 50 && !target; i++) {
  try { target = (await (await fetch(`http://127.0.0.1:${port}/json`)).json()).find((t) => t.type === 'page'); } catch {}
  if (!target) await sleep(200);
}
const ws = new WebSocket(target.webSocketDebuggerUrl);
await new Promise((r) => ws.addEventListener('open', r));
let id = 0; const pending = new Map();
ws.addEventListener('message', (e) => { const m = JSON.parse(e.data); if (m.id && pending.has(m.id)) { pending.get(m.id)(m); pending.delete(m.id); } });
const send = (method, params = {}) => new Promise((r) => { const i = ++id; pending.set(i, r); ws.send(JSON.stringify({ id: i, method, params })); });
await send('Page.enable');
const args = process.argv.slice(2);
for (let i = 0; i < args.length; i += 2) {
  const [out, url] = [args[i], args[i + 1]];
  await send('Emulation.setDeviceMetricsOverride', { width: 1280, height: 900, deviceScaleFactor: 1, mobile: false });
  await send('Page.navigate', { url });
  await sleep(4000);   // fonts + images
  const h = (await send('Runtime.evaluate', { expression: 'Math.ceil(Math.max(document.documentElement.scrollHeight, document.body.scrollHeight, ...[...document.body.children].map(e => e.getBoundingClientRect().bottom + scrollY)))', returnByValue: true })).result.result.value;
  await send('Emulation.setDeviceMetricsOverride', { width: 1280, height: h, deviceScaleFactor: 1, mobile: false });
  await sleep(800);
  const shot = await send('Page.captureScreenshot', { format: 'png', captureBeyondViewport: true, clip: { x: 0, y: 0, width: 1280, height: h, scale: 1 } });
  writeFileSync(out, Buffer.from(shot.result.data, 'base64'));
  console.log(out, h);
}
ws.close(); chrome.kill();
