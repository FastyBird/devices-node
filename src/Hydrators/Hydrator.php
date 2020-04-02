<?php declare(strict_types = 1);

/**
 * Hydrator.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Hydrators
 * @since          0.1.0
 *
 * @date           05.03.18
 */

namespace FastyBird\DevicesNode\Hydrators;

use ArrayAccess;
use Consistence;
use Contributte\Translation;
use DateTimeInterface;
use Doctrine\Common;
use Doctrine\ORM;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\DevicesNode\Hydrators;
use FastyBird\NodeWebServer\Exceptions as NodeWebServerExceptions;
use Fig\Http\Message\StatusCodeInterface;
use IPub\DoctrineCrud;
use IPub\JsonAPIDocument;
use Nette;
use Nette\Utils;
use phpDocumentor;
use Ramsey\Uuid;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use Throwable;

/**
 * Entity hydrator
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
abstract class Hydrator
{

	use Nette\SmartObject;

	protected const IDENTIFIER_KEY = 'id';

	/**
	 * Whether the resource has a client generated id
	 *
	 * @var bool
	 */
	protected $entityIdentifier = false;

	/**
	 * The resource attribute keys to hydrate
	 *
	 * For example:
	 *
	 * ```
	 * $attributes = [
	 *  'foo',
	 *  'bar' => 'baz'
	 * ];
	 * ```
	 *
	 * Will transfer the `foo` resource attribute to the model `foo` attribute, and the
	 * resource `bar` attribute to the model `baz` attribute.
	 *
	 * @var mixed[]|null
	 */
	protected $attributes = null;

	/**
	 * Resource relationship keys that should be automatically hydrated
	 *
	 * @var string[]
	 */
	protected $relationships = [];

	/** @var mixed[]|null */
	private $normalizedAttributes;

	/** @var mixed[]|null */
	private $normalizedRelationships;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	/** @var Common\Annotations\CachedReader */
	private $annotationReader;

	/** @var NodeWebServerExceptions\JsonApiMultipleErrorException */
	private $errors;

	/** @var Translation\PrefixedTranslator */
	protected $translator;

	/** @var string */
	protected $translationDomain = '';

	/**
	 * @param Common\Persistence\ManagerRegistry $managerRegistry
	 * @param Translation\Translator $translator
	 *
	 * @throws Common\Annotations\AnnotationException
	 */
	public function __construct(
		Common\Persistence\ManagerRegistry $managerRegistry,
		Translation\Translator $translator
	) {
		$this->managerRegistry = $managerRegistry;

		$this->annotationReader = new Common\Annotations\CachedReader(
			new Common\Annotations\AnnotationReader(),
			new Common\Cache\ArrayCache()
		);

		$this->errors = new NodeWebServerExceptions\JsonApiMultipleErrorException();

		$this->translator = new Translation\PrefixedTranslator($translator, $this->translationDomain);
	}

	/**
	 * @return string
	 */
	abstract protected function getEntityName(): string;

	/**
	 * @param JsonAPIDocument\Objects\IResourceObject<mixed> $resource
	 * @param DoctrineCrud\Entities\IEntity|null $entity
	 *
	 * @return Utils\ArrayHash
	 *
	 * @throws NodeWebServerExceptions\JsonApiErrorException
	 * @throws NodeWebServerExceptions\JsonApiMultipleErrorException
	 * @throws Throwable
	 */
	public function hydrate(JsonAPIDocument\Objects\IResourceObject $resource, ?DoctrineCrud\Entities\IEntity $entity = null): Utils\ArrayHash
	{
		$entityMapping = $this->mapEntity($this->getEntityName());

		$attributes = $this->hydrateAttributes(
			$this->getEntityName(),
			$resource->getAttributes(),
			$entityMapping,
			$entity,
			null
		);

		$relationships = $this->hydrateRelationships(
			$resource->getRelationships(),
			$entity,
			$entityMapping
		);

		if ($this->errors->hasErrors()) {
			throw $this->errors;
		}

		$result = Utils\ArrayHash::from(array_merge(
			[
				'entity' => $this->getEntityName(),
			],
			$attributes,
			$relationships
		));

		if ($entity === null && $this->entityIdentifier !== false) {
			$identifierKey = !is_string($this->entityIdentifier) ? self::IDENTIFIER_KEY : $this->entityIdentifier;

			try {
				$identifier = $resource->getIdentifier()->getId();

				if (!Uuid\Uuid::isValid($identifier)) {
					throw new NodeWebServerExceptions\JsonApiErrorException(
						StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
						$this->translator->translate('//node.base.messages.validation.identifierInvalid.heading'),
						$this->translator->translate('//node.base.messages.validation.identifierInvalid.message'),
						[
							'pointer' => 'data/id',
						]
					);
				}

				$result[$identifierKey] = Uuid\Uuid::fromString($identifier);

			} catch (JsonAPIDocument\Exceptions\RuntimeException $ex) {
				$result[$identifierKey] = Uuid\Uuid::uuid4();
			}
		}

		return $result;
	}

	/**
	 * @param string $className
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 * @param Hydrators\Fields\IField[] $entityMapping
	 * @param DoctrineCrud\Entities\IEntity|null $entity
	 * @param string|null $rootField
	 *
	 * @return mixed[]
	 */
	protected function hydrateAttributes(
		string $className,
		JsonAPIDocument\Objects\IStandardObject $attributes,
		array $entityMapping,
		?DoctrineCrud\Entities\IEntity $entity,
		?string $rootField
	): array {
		$data = [];

		foreach ($entityMapping as $field) {
			if ($field instanceof Hydrators\Fields\EntityField && $field->isRelationship()) {
				continue;
			}

			// Continue only if attribute is present
			if (!$attributes->has($field->getFieldName())) {
				continue;
			}

			// If there is a specific method for this attribute, we'll hydrate that
			$value = $this->callHydrateAttribute($field->getFieldName(), $attributes, $entity);
			$value = $value ?? $field->getValue($attributes);

			if ($value === null && $field->isRequired() && $entity === null) {
				$this->errors->addError(
					StatusCodeInterface::STATUS_BAD_REQUEST,
					$this->translator->translate('//node.base.messages.validation.parameterMissing.heading'),
					$this->translator->translate('//node.base.messages.validation.parameterMissing.message'),
					[
						'pointer' => 'data/attributes/' . $field->getMappedName(),
					]
				);

			} elseif ($field->isWritable() || ($entity === null && $field->isRequired())) {
				if ($field instanceof Hydrators\Fields\SingleEntityField) {
					// Get attribute entity class name
					$fieldClassName = $field->getClassName();

					/** @var string|JsonAPIDocument\Objects\IStandardObject<mixed> $fieldAttributes */
					$fieldAttributes = $attributes->get($field->getMappedName());

					if ($fieldAttributes instanceof JsonAPIDocument\Objects\IStandardObject) {
						$data[$field->getFieldName()] = $this->hydrateAttributes(
							$fieldClassName,
							$fieldAttributes,
							$this->mapEntity($fieldClassName),
							$entity,
							$field->getMappedName()
						);

						if (!isset($data[$field->getFieldName()]['entity'])) {
							$data[$field->getFieldName()]['entity'] = $fieldClassName;
						}

					} elseif ($value !== null || $field->isNullable()) {
						$data[$field->getFieldName()] = $value;
					}

				} else {
					$data[$field->getFieldName()] = $value;
				}
			}
		}

		if ($rootField === null) {
			try {
				if (class_exists($className)) {
					$rc = new ReflectionClass($className);

					if ($rc->getConstructor() !== null) {
						$constructor = $rc->getConstructor();

						foreach ($constructor->getParameters() as $num => $parameter) {
							if (!$parameter->isVariadic() && $attributes->has($this->getAttributeKey($parameter->getName()) ?? $parameter->getName())) {
								// If there is a specific method for this attribute, we'll hydrate that
								$value = $this->callHydrateAttribute($parameter->getName(), $attributes, $entity);
								$value = $value ?? $attributes->get($this->getAttributeKey($parameter->getName()) ?? $parameter->getName());

								$data[$parameter->getName()] = $value;

							} elseif ($attributes->has($this->getAttributeKey((string) $num) ?? (string) $num)) {
								// If there is a specific method for this attribute, we'll hydrate that
								$value = $this->callHydrateAttribute((string) $num, $attributes, $entity);
								$value = $value ?? $attributes->get($this->getAttributeKey((string) $num) ?? (string) $num);

								$data[$num] = $value;
							}
						}
					}
				}

			} catch (Throwable $ex) {
				// Nothing to do here
			}

		} elseif ($rootField !== null && $attributes->has($rootField . '_type')) {
			try {
				if (class_exists($className)) {
					$rc = new ReflectionClass($className);

					/** @var ORM\Mapping\DiscriminatorMap $classAnnotation */
					$classAnnotation = $this->annotationReader->getClassAnnotation($rc, ORM\Mapping\DiscriminatorMap::class);

					$data['entity'] = $classAnnotation->value[$attributes->get($rootField . '_type')];
				}

			} catch (Throwable $ex) {
				// Annotation not found
			}
		}

		return $data;
	}

	/**
	 * @param JsonAPIDocument\Objects\IRelationships<mixed> $relationships
	 * @param DoctrineCrud\Entities\IEntity|null $entity
	 * @param Hydrators\Fields\IField[] $entityMapping
	 *
	 * @return mixed[]
	 */
	protected function hydrateRelationships(
		JsonAPIDocument\Objects\IRelationships $relationships,
		?DoctrineCrud\Entities\IEntity $entity,
		array $entityMapping
	): array {
		$data = [];

		/** @var JsonAPIDocument\Objects\IRelationship<mixed> $relationship */
		foreach ($relationships->getAll() as $key => $relationship) {
			foreach ($entityMapping as $field) {
				if ($field->getMappedName() === $key) {
					// If there is a specific method for this relationship, we'll hydrate that
					$result = $this->callHydrateRelationship($key, $relationship, $entity);

					if ($result !== null) {
						$data[$field->getFieldName()] = $result;

						continue;
					}

					// If this is a has-one, we'll hydrate it
					if ($relationship->isHasOne()) {
						$relationshipEntity = $this->hydrateHasOne($key, $relationship, $entity, $entityMapping);

						if ($relationshipEntity !== null) {
							$data[$field->getFieldName()] = $relationshipEntity;
						}
					}

				}
			}
		}

		return $data;
	}

	/**
	 * Hydrate a resource has-one relationship
	 *
	 * @param string $resourceKey
	 * @param JsonAPIDocument\Objects\IRelationship<mixed> $relationship
	 * @param DoctrineCrud\Entities\IEntity|null $entity
	 * @param Hydrators\Fields\IField[] $entityMapping
	 *
	 * @return DoctrineCrud\Entities\IEntity|null
	 */
	protected function hydrateHasOne(
		string $resourceKey,
		JsonAPIDocument\Objects\IRelationship $relationship,
		?DoctrineCrud\Entities\IEntity $entity,
		array $entityMapping
	): ?DoctrineCrud\Entities\IEntity {
		foreach ($entityMapping as $field) {
			// Find relationship field
			if ($field->getMappedName() === $resourceKey && $field instanceof Hydrators\Fields\EntityField && $field->isRelationship()) {
				if ($field->isWritable() || ($entity === null && $field->isRequired())) {
					if ($relationship->getIdentifier() !== null) {
						$relationEntity = $this->findRelated($field->getClassName(), $relationship->getIdentifier());

						if ($relationEntity !== null) {
							return $relationEntity;

						} elseif ($entity === null && $field->isRequired()) {
							$this->errors->addError(
								StatusCodeInterface::STATUS_NOT_FOUND,
								$this->translator->translate('//node.base.messages.validation.parameterMissing.heading'),
								$this->translator->translate('//node.base.messages.validation.parameterMissing.message'),
								[
									'pointer' => 'data/relationships/' . $field->getMappedName() . '/data/id',
								]
							);
						}

					} elseif ($entity === null && $field->isRequired()) {
						$this->errors->addError(
							StatusCodeInterface::STATUS_BAD_REQUEST,
							$this->translator->translate('//node.base.messages.validation.parameterMissing.heading'),
							$this->translator->translate('//node.base.messages.validation.parameterMissing.message'),
							[
								'pointer' => 'data/relationships/' . $field->getMappedName() . '/data/id',
							]
						);
					}
				}
			}
		}

		return null;
	}

	/**
	 * Hydrate a relationship by invoking a method on this hydrator.
	 *
	 * @param string $relationshipKey
	 * @param JsonAPIDocument\Objects\IRelationship<mixed> $relationship
	 * @param DoctrineCrud\Entities\IEntity|null $entity
	 *
	 * @return mixed[]|DoctrineCrud\Entities\IEntity|null
	 */
	private function callHydrateRelationship(
		string $relationshipKey,
		JsonAPIDocument\Objects\IRelationship $relationship,
		?DoctrineCrud\Entities\IEntity $entity = null
	) {
		$method = $this->methodForRelationship($relationshipKey);

		if (empty($method) || !method_exists($this, $method)) {
			return null;
		}

		$callable = [$this, $method];

		if (is_callable($callable)) {
			$result = call_user_func($callable, $relationship, $entity);

			if (
				$result === null
				|| is_array($result)
				|| $result instanceof DoctrineCrud\Entities\IEntity
			) {
				return $result;
			}

			throw new Exceptions\InvalidStateException(sprintf('Relationship have to be an array or entity instance, %s provided.', get_class($result)));
		}

		return null;
	}

	/**
	 * Hydrate a attribute by invoking a method on this hydrator.
	 *
	 * @param string $attributeKey
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 * @param DoctrineCrud\Entities\IEntity|null $entity
	 *
	 * @return mixed|null
	 */
	private function callHydrateAttribute(
		string $attributeKey,
		JsonAPIDocument\Objects\IStandardObject $attributes,
		?DoctrineCrud\Entities\IEntity $entity = null
	) {
		$method = $this->methodForAttribute($attributeKey);

		if (empty($method) || !method_exists($this, $method)) {
			return null;
		}

		$callable = [$this, $method];

		if (is_callable($callable)) {
			return call_user_func($callable, $attributes, $entity);
		}

		return null;
	}

	/**
	 * @param string $entityClassName
	 * @param JsonAPIDocument\Objects\IResourceIdentifier<mixed> $identifier
	 *
	 * @return DoctrineCrud\Entities\IEntity|null
	 */
	private function findRelated(
		string $entityClassName,
		JsonAPIDocument\Objects\IResourceIdentifier $identifier
	): ?DoctrineCrud\Entities\IEntity {
		if (!Uuid\Uuid::isValid($identifier->getId())) {
			return null;
		}

		if (!class_exists($entityClassName)) {
			return null;
		}

		$entityManager = $this->managerRegistry->getManagerForClass($entityClassName);

		if ($entityManager !== null) {
			/** @var DoctrineCrud\Entities\IEntity|null $entity */
			$entity = $entityManager
				->getRepository($entityClassName)
				->find($identifier->getId());

			return $entity;
		}

		return null;
	}

	/**
	 * @param string $entityKey
	 *
	 * @return string|null
	 */
	private function getAttributeKey(string $entityKey): ?string
	{
		$this->normalizeAttributes();

		return $this->normalizedAttributes[$entityKey] ?? null;
	}

	/**
	 * @return void
	 */
	private function normalizeAttributes(): void
	{
		if (is_array($this->normalizedAttributes)) {
			return;
		}

		$this->normalizedAttributes = [];

		if ($this->attributes !== null) {
			foreach ($this->attributes as $resourceKey => $entityKey) {
				if (is_numeric($resourceKey)) {
					$resourceKey = $entityKey;
				}

				$this->normalizedAttributes[$entityKey] = $resourceKey;
			}
		}
	}

	/**
	 * Get the model method name for a resource relationship key
	 *
	 * @param string $entityKey
	 *
	 * @return string|null
	 */
	private function getRelationshipKey(string $entityKey): ?string
	{
		$this->normalizeRelationships();

		return $this->normalizedRelationships[$entityKey] ?? null;
	}

	/**
	 * @return void
	 */
	private function normalizeRelationships(): void
	{
		if (is_array($this->normalizedRelationships)) {
			return;
		}

		$this->normalizedRelationships = [];

		if ($this->relationships !== null) {
			foreach ($this->relationships as $resourceKey => $entityKey) {
				if (is_numeric($resourceKey)) {
					$resourceKey = $entityKey;
				}

				$this->normalizedRelationships[$entityKey] = $resourceKey;
			}
		}
	}

	/**
	 * @param string $entityClassName
	 *
	 * @return Hydrators\Fields\IField[]
	 */
	protected function mapEntity(string $entityClassName): array
	{
		$entityManager = $this->managerRegistry->getManagerForClass($entityClassName);

		if ($entityManager === null) {
			return [];
		}

		/** @var ORM\Mapping\ClassMetadata $classMetadata */
		$classMetadata = $entityManager->getClassMetadata($entityClassName);

		$reflectionProperties = [];

		try {
			if (class_exists($entityClassName)) {
				$rc = new ReflectionClass($entityClassName);

			} else {
				throw new Exceptions\InvalidStateException('Entity could not be parsed');
			}

		} catch (ReflectionException $ex) {
			throw new Exceptions\InvalidStateException('Entity could not be parsed');
		}

		foreach ($rc->getProperties() as $rp) {
			$reflectionProperties[] = $rp->getName();
		}

		$entityFields = array_unique(array_merge(
			$reflectionProperties,
			$classMetadata->getFieldNames(),
			$classMetadata->getAssociationNames()
		));

		$fields = [];

		foreach ($entityFields as $fieldName) {
			try {
				// Check if property in entity class exists
				$rp = $rc->getProperty($fieldName);

			} catch (ReflectionException $ex) {
				continue;
			}

			/** @var DoctrineCrud\Mapping\Annotation\Crud $crud */
			$crud = $this->annotationReader->getPropertyAnnotation($rp, DoctrineCrud\Mapping\Annotation\Crud::class);

			if (!$crud instanceof DoctrineCrud\Mapping\Annotation\Crud) {
				continue;
			}

			// Check if field is updatable
			if (!$crud->isRequired() && !$crud->isWritable()) {
				continue;
			}

			$isRelationship = false;

			if ($this->getRelationshipKey($fieldName) !== null) {
				// Transform entity field name to schema relationship name
				$mappedKey = $this->getRelationshipKey($fieldName);

				$isRelationship = true;

			} elseif ($this->getAttributeKey($fieldName) !== null) {
				$mappedKey = $this->getAttributeKey($fieldName);

			} else {
				continue;
			}

			// Extract all entity property annotations
			$propertyAnnotations = array_map((function ($annotation): string {
				return get_class($annotation);
			}), $this->annotationReader->getPropertyAnnotations($rp));

			if (in_array(ORM\Mapping\OneToOne::class, $propertyAnnotations, true)) {
				/** @var ORM\Mapping\OneToOne $mapping */
				$mapping = $this->annotationReader->getPropertyAnnotation($rp, ORM\Mapping\OneToOne::class);
				$className = $mapping->targetEntity;

				// Check if class is callable
				if (is_string($className) && class_exists($className)) {
					$fields[] = new Hydrators\Fields\SingleEntityField($className, false, $mappedKey, $isRelationship, $fieldName, $crud->isRequired(), $crud->isWritable());
				}

			} elseif (in_array(ORM\Mapping\OneToMany::class, $propertyAnnotations, true)) {
				/** @var ORM\Mapping\OneToMany $mapping */
				$mapping = $this->annotationReader->getPropertyAnnotation($rp, ORM\Mapping\OneToMany::class);
				$className = $mapping->targetEntity;

				// Check if class is callable
				if (is_string($className) && class_exists($className)) {
					$fields[] = new Hydrators\Fields\CollectionField($className, true, $mappedKey, $isRelationship, $fieldName, $crud->isRequired(), $crud->isWritable());
				}

			} elseif (in_array(ORM\Mapping\ManyToMany::class, $propertyAnnotations, true)) {
				/** @var ORM\Mapping\ManyToMany $mapping */
				$mapping = $this->annotationReader->getPropertyAnnotation($rp, ORM\Mapping\ManyToMany::class);
				$className = $mapping->targetEntity;

				// Check if class is callable
				if (is_string($className) && class_exists($className)) {
					$fields[] = new Hydrators\Fields\CollectionField($className, true, $mappedKey, $isRelationship, $fieldName, $crud->isRequired(), $crud->isWritable());
				}

			} elseif (in_array(ORM\Mapping\ManyToOne::class, $propertyAnnotations, true)) {
				/** @var ORM\Mapping\ManyToOne $mapping */
				$mapping = $this->annotationReader->getPropertyAnnotation($rp, ORM\Mapping\ManyToOne::class);
				$className = $mapping->targetEntity;

				// Check if class is callable
				if (is_string($className) && class_exists($className)) {
					$fields[] = new Hydrators\Fields\SingleEntityField($className, false, $mappedKey, $isRelationship, $fieldName, $crud->isRequired(), $crud->isWritable());
				}

			} else {
				$varAnnotation = $this->parseAnnotation($rp, 'var');

				if ($varAnnotation === null) {
					continue;
				}

				$className = null;

				$isString = false;
				$isNumber = false;
				$isDecimal = false;
				$isArray = false;
				$isBool = false;
				$isClass = false;

				$isNullable = false;

				$typesFound = 0;

				try {
					$rm = $rc->getMethod('get' . ucfirst($fieldName));

					$returnType = $rm->getReturnType();

					if ($returnType instanceof ReflectionNamedType) {
						$varAnnotation = $returnType->getName() . ($returnType->allowsNull() ? '|null' : '');
					}

				} catch (ReflectionException $ex) {
					// Nothing to do
				}

				if (strpos($varAnnotation, '|') !== false) {
					$varDatatypes = explode('|', $varAnnotation);

				} else {
					$varDatatypes = [$varAnnotation];
				}

				$className = null;

				foreach ($varDatatypes as $varDatatype) {
					if (class_exists($varDatatype) || interface_exists($varDatatype)) {
						$className = (string) $varDatatype;

						$isClass = true;

						$typesFound++;

					} elseif (strtolower($varDatatype) === 'string') {
						$isString = true;

						$typesFound++;

					} elseif (strtolower($varDatatype) === 'int') {
						$isNumber = true;

						$typesFound++;

					} elseif (strtolower($varDatatype) === 'float') {
						$isDecimal = true;

						$typesFound++;

					} elseif (strtolower($varDatatype) === 'array' || strtolower($varDatatype) === 'mixed[]') {
						$isArray = true;

						$typesFound++;

					} elseif (strtolower($varDatatype) === 'bool') {
						$isBool = true;

						$typesFound++;

					} elseif (strtolower($varDatatype) === 'null') {
						$isNullable = true;
					}
				}

				if ($typesFound > 0) {
					if ($typesFound > 1) {
						$fields[] = new Hydrators\Fields\MixedField(
							$isNullable,
							$mappedKey,
							$fieldName,
							$crud->isRequired(),
							$crud->isWritable()
						);

					} elseif ($isClass && $className !== null) {
						try {
							$typeRc = new ReflectionClass($className);

							if ($typeRc->implementsInterface(DateTimeInterface::class) || $className === DateTimeInterface::class) {
								$fields[] = new Hydrators\Fields\DateTimeField(
									$isNullable,
									$mappedKey,
									$fieldName,
									$crud->isRequired(),
									$crud->isWritable()
								);

							} elseif ($typeRc->isSubclassOf(Consistence\Enum\Enum::class)) {
								$fields[] = new Hydrators\Fields\EnumField(
									$className,
									$isNullable,
									$mappedKey,
									$fieldName,
									$crud->isRequired(),
									$crud->isWritable()
								);

							} elseif ($typeRc->implementsInterface(ArrayAccess::class)) {
								$fields[] = new Hydrators\Fields\ArrayField(
									$isNullable,
									$mappedKey,
									$fieldName,
									$crud->isRequired(),
									$crud->isWritable()
								);

							} else {
								$fields[] = new Hydrators\Fields\SingleEntityField(
									$className,
									$isNullable,
									$mappedKey,
									$isRelationship,
									$fieldName,
									$crud->isRequired(),
									$crud->isWritable()
								);
							}

						} catch (ReflectionException $ex) {
							$fields[] = new Hydrators\Fields\SingleEntityField(
								$className,
								$isNullable,
								$mappedKey,
								$isRelationship,
								$fieldName,
								$crud->isRequired(),
								$crud->isWritable()
							);
						}

					} elseif ($isString) {
						$fields[] = new Hydrators\Fields\TextField(
							$isNullable,
							$mappedKey,
							$fieldName,
							$crud->isRequired(),
							$crud->isWritable()
						);

					} elseif ($isNumber || $isDecimal) {
						$fields[] = new Hydrators\Fields\NumberField(
							$isDecimal,
							$isNullable,
							$mappedKey,
							$fieldName,
							$crud->isRequired(),
							$crud->isWritable()
						);

					} elseif ($isArray) {
						$fields[] = new Hydrators\Fields\ArrayField(
							$isNullable,
							$mappedKey,
							$fieldName,
							$crud->isRequired(),
							$crud->isWritable()
						);

					} elseif ($isBool) {
						$fields[] = new Hydrators\Fields\BooleanField(
							$isNullable,
							$mappedKey,
							$fieldName,
							$crud->isRequired(),
							$crud->isWritable()
						);
					}
				}
			}
		}

		return $fields;
	}

	/**
	 * Return the method name to call for hydrating the specific relationship.
	 *
	 * If this method returns an empty value, or a value that is not callable, hydration
	 * of the the relationship will be skipped
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	private function methodForRelationship(string $key): string
	{
		return sprintf('hydrate%sRelationship', $this->classify($key));
	}

	/**
	 * Return the method name to call for hydrating the specific attribute.
	 *
	 * If this method returns an empty value, or a value that is not callable, hydration
	 * of the the relationship will be skipped
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	private function methodForAttribute(string $key): string
	{
		return sprintf('hydrate%sAttribute', $this->classify($key));
	}

	/**
	 * Gets the upper camel case form of a string.
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	private function classify(string $value): string
	{
		$converted = ucwords(str_replace(['-', '_'], ' ', $value));

		return str_replace(' ', '', $converted);
	}

	/**
	 * @param ReflectionProperty $rp
	 * @param string $name
	 *
	 * @return string|null
	 */
	private function parseAnnotation(ReflectionProperty $rp, string $name): ?string
	{
		if ($rp->getDocComment() === false) {
			return null;
		}

		$factory = phpDocumentor\Reflection\DocBlockFactory::createInstance();
		$docblock = $factory->create($rp->getDocComment());

		foreach ($docblock->getTags() as $tag) {
			if ($tag->getName() === $name) {
				return trim((string) $tag);
			}
		}

		return null;
	}

}
