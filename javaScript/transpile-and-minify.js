// transpile-and-minify.js
const fs = require('fs');
const path = require('path');
const babel = require('@babel/core');
const terser = require('terser');

const inputDir = path.join(__dirname, 'src');
const outputDir = path.join(__dirname, '/../web/static/js');

function ensureDirSync(dir) {
    if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });
}

function getFilesRecursively(dir, ext = '.jsx') {
    const entries = fs.readdirSync(dir, { withFileTypes: true });
    return entries.flatMap(entry => {
        const res = path.resolve(dir, entry.name);
        return entry.isDirectory() ? getFilesRecursively(res, ext) :
            entry.name.endsWith(ext) ? [res] : [];
    });
}

async function transpileAndMinify() {
    ensureDirSync(outputDir);
    const files = getFilesRecursively(inputDir);
    for (const file of files) {
        const { code } = await babel.transformFileAsync(file, { filename: file });
        const componentName = path.basename(file).replace(/\.jsx$/, '');
        const wrappedCode = `window.${componentName} = (function(){\n${code}\nreturn exports.default || exports;\n})();`;
        const minified = await terser.minify(wrappedCode);
        const relativePath = path.relative(inputDir, file).replace(/\.jsx$/, '.js');
        const outputFile = path.join(outputDir, relativePath);
        ensureDirSync(path.dirname(outputFile));
        fs.writeFileSync(outputFile, minified.code, 'utf-8');
        console.log(`✔ ${outputFile}`);
    }
}

transpileAndMinify().catch(err => console.error(err));
