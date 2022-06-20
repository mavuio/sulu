import { defineConfig } from 'vite'
import { svelte } from '@sveltejs/vite-plugin-svelte'
import preprocess from 'svelte-preprocess';

import fs from 'fs';

const production = process.env.NODE_ENV == 'production';


// https://vitejs.dev/config/
export default defineConfig({
  server: {
    host: 'hortencollection.com.test',
    https:{
      key: fs.readFileSync('/Users/manfred/.config/valet/Certificates/hortencollection.com.test.key'),
      cert: fs.readFileSync('/Users/manfred/.config/valet/Certificates/hortencollection.com.test.crt')
    }
  },
  plugins: [svelte({
    preprocess: preprocess({
      postcss: true
    })
  })],
  base: './',
  esbuild: {
    minify:true
  },
  build: {
    minify: 'esbuild',
    lib: {
      entry: production ? "./src/tabbar/tabbar.js" : "./src/tabbar/tabbar_dev.js",
      formats: ['esm']
    },
    rollupOptions: {
      output: {
        dir: '../../public/be_assets',
        entryFileNames: '[name].js',
        assetFileNames: (assetInfo) => {
          if (assetInfo.name == 'style.css')
            return 'tabbar.css';
          return assetInfo.name;
        },
      }
    },
  }
})
