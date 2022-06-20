<script>
  import { onMount, tick } from "svelte";

  export let props;

  export let helpers;

  let data = props.data;

  let selectedKey = "";

  let tabs = [];

  function setTab(val) {
    selectedKey = val;
    props.onChange(selectedKey);
  }

  onMount((args) => {
    // console.log("#log 6113 SVELTE LOADED TABBAR ➜ props", props);
    // console.log("#log 6113 SVELTE LOADED TABBAR ➜ helpers", helpers);

    tabs = helpers
      .toJS(props.schemaOptions.tabs)
      .value.map(({ name, title }) => {
        return { key: name, label: title };
      });

    // console.log("#log 4191 tabs", tabs);

    // always activate first tab on load:
    setTab(tabs[0].key);
  });
</script>

<div class="tw">
  <div class="tabbar-wrapper">
    <div class="tabbar">
      <div class="placeholder" />
      {#each tabs as tab}
        <button
          on:click={() => setTab(tab.key)}
          class={tab.key == selectedKey ? "active" : "inactive"}
          >{tab.label}</button
        >
      {/each}
      <div class="placeholder" />
    </div>
    <div class="tabbar-stripe {`tabbar-stripe-${selectedKey}`}" />
  </div>
</div>

<style global lang="postcss">
  .tw {
    @tailwind utilities;
    @tailwind components;
    @tailwind base;

    .tabbar {
      display: flex;

      .placeholder {
        flex: 1 1 auto;
        border-bottom: 1px solid #ccc;
      }

      .placeholder:first-child {
        flex: 0 0 1rem;
        width: 1rem;
      }

      button {
        border: 1px solid #ccc;
        border-top-left-radius: 2px;
        border-top-right-radius: 2px;
        border-right-color: transparent;
        padding: 0.4rem 1rem;
        background: none;
        cursor: pointer;
      }

      button:last-of-type {
        border-right-color: #ccc;
      }

      button.active {
        border-bottom-color: transparent;
      }
    }
    .tabbar-stripe {
      height: 15px;
      position: relative;
    }

    .tabbar-stripe:after {
      content: "";
      position: absolute;
      z-index: 1;
      bottom: 0;
      left: 0;
      pointer-events: none;
      background-image: linear-gradient(
        to bottom,
        rgba(255, 255, 255, 0),
        rgba(255, 255, 255, 1) 90%
      );
      width: 100%;
      height: 15px;
    }
    .tabbar-stripe-styles,
    .tab-styles.active {
      background-color: yellow;
    }
  }
</style>
