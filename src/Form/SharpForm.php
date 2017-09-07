<?php

namespace Code16\Sharp\Form;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\Layout\FormLayoutTab;
use Code16\Sharp\Utils\Transformers\WithCustomTransformers;

abstract class SharpForm
{
    use WithCustomTransformers, HandleFormFields;

    /**
     * @var array
     */
    protected $tabs = [];

    /**
     * @var bool
     */
    protected $tabbed = true;

    /**
     * @var bool
     */
    protected $layoutBuilt = false;

    /**
     * Return the form fields layout.
     *
     * @return array
     */
    function formLayout(): array
    {
        if(!$this->layoutBuilt) {
            $this->buildFormLayout();
            $this->layoutBuilt = true;
        }

        return [
            "tabbed" => $this->tabbed,
            "tabs" => collect($this->tabs)->map(function($tab) {
                return $tab->toArray();
            })->all()
        ];
    }

    /**
     * Return the entity instance, as an array.
     *
     * @param $id
     * @return array
     */
    function instance($id): array
    {
        return collect($this->find($id))
            // Filter model attributes on actual form fields
            ->only($this->getDataKeys())
            ->all();
    }

    /**
     * Return a new entity instance, as an array.
     *
     * @return array
     */
    public function newInstance()
    {
        $data = collect($this->create())
            // Filter model attributes on actual form fields
            ->only($this->getDataKeys())
            ->all();

        return sizeof($data) ? $data : null;
    }

    /**
     * @param string $label
     * @param \Closure|null $callback
     * @return $this
     */
    protected function addTab(string $label, \Closure $callback = null)
    {
        $this->layoutBuilt = false;

        $tab = $this->addTabLayout(new FormLayoutTab($label));

        if($callback) {
            $callback($tab);
        }

        return $this;
    }

    /**
     * @param int $size
     * @param \Closure|null $callback
     * @return $this
     */
    protected function addColumn(int $size, \Closure $callback = null)
    {
        $this->layoutBuilt = false;

        $column = $this->getLonelyTab()->addColumnLayout(
            new FormLayoutColumn($size)
        );

        if($callback) {
            $callback($column);
        }

        return $this;
    }

    /**
     * @param bool $tabbed
     * @return $this
     */
    protected function setTabbed(bool $tabbed = true)
    {
        $this->tabbed = $tabbed;

        return $this;
    }

    /**
     * @param FormLayoutTab $tab
     * @return FormLayoutTab
     */
    private function addTabLayout(FormLayoutTab $tab): FormLayoutTab
    {
        $this->tabs[] = $tab;

        return $tab;
    }

    /**
     * @return FormLayoutTab
     */
    private function getLonelyTab()
    {
        if(!sizeof($this->tabs)) {
            $this->addTabLayout(new FormLayoutTab("one"));
        }

        return $this->tabs[0];
    }

    /**
     * @param $id
     * @param $data
     * @return mixed
     */
    public function updateInstance($id, $data)
    {
        return $this->update($id, $this->formatRequestData($data));
    }

    /**
     * @param $data
     * @return mixed
     */
    public function storeInstance($data)
    {
        return $this->store($this->formatRequestData($data));
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function store(array $data)
    {
        return $this->update(null, $data);
    }

    /**
     * Pack new Model data as JSON.
     *
     * @return array
     */
    public function create(): array
    {
        return collect($this->getDataKeys())
            ->flip()
            ->map(function() {
                return null;
            })->all();
    }

    /**
     * Retrieve a Model for the form and pack all its data as JSON.
     *
     * @param $id
     * @return array
     */
    abstract function find($id): array;

    /**
     * @param $id
     * @param array $data
     */
    abstract function update($id, array $data);

    /**
     * @param $id
     */
    abstract function delete($id);

    /**
     * Build form fields using ->addField()
     *
     * @return void
     */
    abstract function buildFormFields();

    /**
     * Build form layout using ->addTab() or ->addColumn()
     *
     * @return void
     */
    abstract function buildFormLayout();
}