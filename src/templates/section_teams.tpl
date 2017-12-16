{include "head.tpl"}

<ol class="breadcrumb">
  <li><a href="{mkurl action="section"}">Sections</a></li>
  <li><a href="{mkurl action="section" page="details" section=$section->section_id}">{$section->section_name|escape}</a></li>
  <li class="active">Groupes de diffusion</li>
</ol>

{include "section_head.tpl"}

<h3>Équipes de {$section->section_name|escape}</h3>

{include "foot.tpl"}
