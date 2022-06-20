<script>
  import { slide } from "svelte/transition";
  import { quintOut } from "svelte/easing";
  import Pagination from "./Pagination.svelte";

  import DateFormatter from "./date_formatter";
  export let v_id;
  export let month = "";
  export let lang = "de";
  export let collapsed = true;
  export let productNr = "";

  let open = false;

  let current_page = 1;

  $: paging = {
    current_page: 1,
    per_page: 10,
  };

  let termine = null;
  let infotext = null;

  function toggleOpen(event) {
    if (!open) {
      fetchTermine();
      open = true;
    } else {
      open = false;
    }
  }

  function fetchTermine() {
    let url = `/api/veranstaltung/${v_id}`;

    if (month) {
      url += `?month=${month}`;
    }

    fetch(url)
      .then((response) => response.json())
      .then((payload) => {
        termine = payload.items;
        infotext = payload.infotext;
      });
  }

  function toDate(datestr) {
    return new Date(datestr);
  }

  function openPretix(pretixId) {
    if (window.PretixWidget) {
      let items = null;
      if (productNr != "") {
        items = [{ item: `item_${productNr}`, count: 1 }];
      }

      window.PretixWidget.open(
        "https://shop.hortencollection.com/tickets/",
        null,
        pretixId,
        items
      );
    }
  }

  function reloadPretixWidget() {
    const iframe = document.querySelector(".pretix-widget-overlay iframe");
    console.log("#log 4373 reload", iframe);
    iframe.src = iframe.src;
  }

  // function openTimeSlots() {
  //   if(window.PretixWidget) {
  //     window.PretixWidget.open('https://shop.hortencollection.com/tickets/');
  //     setTimeout(reloadPretixWidget, 1500);
  //   }
  // }

  if (!collapsed) {
    toggleOpen();
  }

  function paginate(array, page_size, page_number) {
    // human-readable page numbers usually start with 1, so we reduce 1 in the first argument
    return array.slice((page_number - 1) * page_size, page_number * page_size);
  }

  // if(v_id==10) {
  //   toggleOpen();
  // }
</script>

<div>
  <!-- <div class="mt-4 preset_boxbutton font-soleil">
    <a href={"#"} class="!inline-flex items-center c1" on:click|preventDefault={openTimeSlots}>
      
    
      <span>slots</span></a>
    </div>
   -->
  {#if collapsed}
    <div class="mt-4 preset_boxbutton font-soleil">
      <a
        href={"#"}
        class="!inline-flex items-center c1"
        on:click|preventDefault={toggleOpen}
      >
        <span
          class:rotate-90={open}
          class="relative h-4 overflow-hidden transition-all -top-1"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="w-5 h-5"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            strokeWidth="10"
          >
            <path d="M9 5l7 7-7 7" />
          </svg>
        </span>
        <span>Termine & Tickets</span></a
      >
    </div>
  {/if}

  {#if open}
    <div
      class="mt-4 font-soleil"
      transition:slide={{ duration: 300, easing: quintOut }}
    >
      {#if termine == null}
        <img
          src="/img/loading.gif"
          class="block w-auto h-12 mx-auto invert"
          alt="loading..."
        />
      {:else}
        {#each paginate(termine, paging.per_page, paging.current_page) as t, idx}
          <div class="flex items-center justify-between w-full my-2 ">
            <div>
              {#if t.date2 != t.date1}
                {DateFormatter.formatDate(
                  toDate(t.date1),
                  "DDD, dd.",
                  lang
                )}–{DateFormatter.formatDate(
                  toDate(t.date2),
                  "DDD, dd. MMM",
                  lang
                )}{:else}
                {DateFormatter.formatDate(
                  toDate(t.date1),
                  "DDD, dd. MMM",
                  lang
                )}{/if}{#if t.time1}
                , {t.time1}{#if t.time2 && t.time2 != t.time1 && !t.pretixId}–{t.time2}
                {/if}
              {/if}
              {#if t.text}
                <div class="text-xs">{t.text}</div>
              {/if}
            </div>

            {#if t.pretixId}
              <div class="self-end grow-0  shrink-0 w-[8rem] ">
                <!-- {#if idx > 6 && t.pretixId % 2 == 0}
                  <div class="preset_boxbutton">
                    <span
                      class="c1  !bg-transparent border !text-white !w-full text-center"
                      >Ausverkauft</span
                    >
                  </div>
                {:else} -->
                <span class=" preset_boxbutton">
                  <a
                    href={"#"}
                    on:click|preventDefault={openPretix(t.pretixId)}
                    class="c1 !w-full text-center">Tickets</a
                  >
                </span>
                <!-- {/if} -->
              </div>
            {/if}
          </div>
        {:else}
          <div class="p-4 text-xs border">keine aktuellen Termine gefunden</div>
        {/each}

        {#if termine.length > paging.per_page}
          <Pagination
            bind:current={paging.current_page}
            num_items={termine.length}
            per_page={paging.per_page}
          />
        {/if}

        {#if infotext}
          <div class="mt-4 text-xs infotext">{@html infotext}</div>
        {/if}
      {/if}
    </div>
  {/if}
  <div />
</div>
