<script>
  import { onMount } from "svelte";
  import editorWorker from "monaco-editor/esm/vs/editor/editor.worker?worker";
  import jsonWorker from "monaco-editor/esm/vs/language/json/json.worker?worker";
  import cssWorker from "monaco-editor/esm/vs/language/css/css.worker?worker";
  import htmlWorker from "monaco-editor/esm/vs/language/html/html.worker?worker";
  import tsWorker from "monaco-editor/esm/vs/language/typescript/ts.worker?worker";
  import TailwindcssWorker from "monaco-tailwindcss/tailwindcss.worker.js?worker";

  import * as monaco from "monaco-editor";

  import {
    configureMonacoTailwindcss,
    tailwindcssData,
  } from "monaco-tailwindcss";

  let divEl = null;
  let editor;
  let Monaco;

  export let props;

  let code = "n/a";
  code = props.value;

  const tailwindConfig = {
    theme: {
      extend: {
        colors: {
          gray_on_bg: "#969696",
          wood: "#904628",
          darkbg: "#191919",
          lightbg: "#ffffff",
          pagecolor: "#D8E2DA",
          footer_textcolor: "#666a67",
        },
      },
    },
  };

  let expanded = false;

  monaco.languages.css.cssDefaults.setOptions({
    data: {
      dataProviders: {
        tailwindcssData,
      },
    },
  });

  monaco.languages.css.scssDefaults.setOptions({
    lint: { unknownAtRules: "ignore" },
  });

  configureMonacoTailwindcss(monaco, {
    languageSelector: ["scss", "javascript", "html", "mdx", "typescript"],
    tailwindConfig,
  });

  onMount(() => {
    self.MonacoEnvironment = {
      getWorker: function (_moduleId, label) {
        console.log("#log 1312 get worker ", label);
        if (label === "json") {
          return new jsonWorker();
        }
        if (label === "css" || label === "scss" || label === "less") {
          return new cssWorker();
        }
        if (label === "html" || label === "handlebars" || label === "razor") {
          return new htmlWorker();
        }
        if (label === "typescript" || label === "javascript") {
          return new tsWorker();
        }
        if (label === "tailwindcss") {
          return new TailwindcssWorker();
        }

        return new editorWorker();
      },
    };

    editor = monaco.editor.create(divEl, {
      value: code,
      language: "scss",
      theme: "vs-dark",
      minimap: {
        enabled: false,
      },
      automaticLayout: true,
    });

    editor.onDidChangeModelContent(function (e) {
      props.onChange(editor.getModel().getValue());
    });

    return () => {
      editor.dispose();
    };
  });

  function toggleExpanded() {
    expanded = !expanded;
  }
</script>

<div class="tw">
  <div class="relative overflow-visible codearea-wrapper">
    <div class="absolute right-0 flex justify-end -top-5">
      <button on:click={toggleExpanded} class="btn">
        {#if expanded} ↑ collapse {:else} ↓ expand {/if}
      </button>
    </div>
    <div class={expanded ? "h-[90vh]" : "h-[40vh]"}>
      <div bind:this={divEl} class="h-full" />
    </div>
  </div>
</div>

<style global lang="postcss">
  .tw {
    @tailwind utilities;
    @tailwind components;
    @tailwind base;
  }
</style>
