<script>
  export let lang;

  export let isHot = false;
  export let targetgroups;
  export let event_types;

  export let query = {
    targetgroup: "",
    event_type: "",
    date1: "",
    date2: "",
  };

  let form;
  let submitting = false;

  function onChange() {
    submitting = true;
    form.submit();
  }
</script>

<div class="eventsuche">
  <form
    method="GET"
    {lang}
    bind:this={form}
    class:opacity-40={submitting}
    class:pointer-events-none={submitting}
    class:animate-pulse={submitting}
  >
    {#if isHot}
      <input type="hidden" name="hot" value="1" />
    {/if}
    <input
      type="date"
      name="date1"
      bind:value={query.date1}
      on:change={onChange}
    />
    <input
      type="date"
      name="date2"
      bind:value={query.date2}
      on:change={onChange}
    />

    <select
      name="event_type"
      bind:value={query.event_type}
      class:!bg-white={query.event_type}
      class="!bg-opacity-40"
      on:change={onChange}
    >
      <option value="">Veranstaltungstyp</option>
      {#each event_types as [key, text]}
        <option value={key}>{text}</option>
      {/each}
    </select>

    <select
      name="targetgroup"
      bind:value={query.targetgroup}
      class:!bg-white={query.targetgroup}
      class="!bg-opacity-40"
      on:change={onChange}
    >
      <option value="">Zielgruppe</option>
      {#each targetgroups as [key, text]}
        <option value={key}>{text}</option>
      {/each}
    </select>
  </form>
</div>
