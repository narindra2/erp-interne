
@php
    $placeholder =  $placeholder ?? "Internes/Collaborateurs/Utilisateurs";
@endphp
<input type="text" class="form-control form-control-solid"  autocomplete="off" name="users" value="{{ isset($default) ? $default : "" }}" id="users-tagify" placeholder="{{ $placeholder}}"/>
<input type="hidden" id="users-tagify-values" value=""/>
<script>
    $(document).ready(function() {
        var input = document.querySelector("#users-tagify");
        function tagTemplate(tagData){
            return `
                <tag title="${tagData.name}"
                        contenteditable='false'
                        spellcheck='false'
                        tabIndex="-1"
                        class="tagify__tag ${tagData.class ? tagData.class : ""}" ${this.getAttributes(tagData)}>
                    <x title='' class='tagify__tag__removeBtn' role='button' aria-label='remove tag'></x>
                    <div>
                        <div class='tagify__tag__avatar-wrap'>
                            <img onerror="this.style.visibility='hidden'"  class="rounded-circle w-25px me-2" src="${tagData.avatar}">
                        </div>
                        <span class='tagify__tag-text'>${tagData.name}</span>
                    </div>
                </tag>
            `
        }

        function suggestionItemTemplate(tagData){
            return `
                <div ${this.getAttributes(tagData)}
                    class='tagify__dropdown__item ${tagData.class ? tagData.class : ""}'
                    tabindex="0"
                    role="option">
                    ${ tagData.avatar ? `
                        <b class='tagify__dropdown__item__avatar-wrap me-2'>
                            <img onerror="this.style.visibility='hidden'" class="rounded-circle w-25px me-2"  src="${tagData.avatar}">
                        </b>` : ''
                    }
                    <strong>${tagData.name}</strong>
                    <span class="badge badge-exlusive badge-light-primary fw-bold fs-8"> ${tagData.job}</span>
                </div>
            `
        }
        var tagify = new Tagify(input, {
            tagTextProp: 'name',
            // enforceWhitelist: true,
            keepInvalidTags: false,
            skipInvalid: true,
            whitelist: @json($users),
            // maxTags: 1,
            dropdown: {
                maxItems: @json(count($users)),
                enabled: 0,
                classname: 'users-list',
                searchKeys: ['name', 'email']
            },
            templates: {
                dropdownItem: suggestionItemTemplate,
                tag: tagTemplate
            },
        });
        // tagify.on('add', onAddTag)
        // tagify.on('remove', removeTag)
        // function onAddTag(e) {
        //     var value = e.detail.data.value
        //     !value ? $("#users-tagify-values").val(0) :   tagify.removeAllTags(); $("#users-tagify-values").val(value);
        // }
        // function removeTag(e) {
        //     $("#users-tagify-values").val(0);
        // }
    })
</script>