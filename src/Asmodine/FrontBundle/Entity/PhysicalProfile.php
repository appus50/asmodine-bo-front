<?php

namespace Asmodine\FrontBundle\Entity;

use Asmodine\CommonBundle\DTO\PhysicalProfileDTO;
use Asmodine\CommonBundle\Exception\NullException;
use Asmodine\CommonBundle\Util\Str;
use Asmodine\FrontBundle\Entity\Traits\LifeCycle;
use Doctrine\ORM\Mapping as ORM;

/**
 * PhysicalProfile.
 *
 * @ORM\Table(
 *     name="front_orm_physical_profile",
 *     indexes={
 *          @ORM\Index(name="user_idx", columns={"user_id"}),
 *          @ORM\Index(name="current_idx", columns={"user_id", "current"}),
 *      },
 * )
 * @ORM\Entity(repositoryClass="Asmodine\FrontBundle\Repository\PhysicalProfileRepository")
 * @ORM\EntityListeners({"Asmodine\FrontBundle\Entity\Listener\PhysicalProfileListener"})
 * @ORM\HasLifecycleCallbacks()
 */
class PhysicalProfile
{
    use LifeCycle;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(inversedBy="physicalProfiles", targetEntity="User")
     * @ORM\JoinColumn(referencedColumnName="id", name="user_id")
     */
    private $user;

    /**
     * @var bool
     *
     * @ORM\Column(name="current", type="boolean")
     */
    private $current;

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @var int
     *
     * @ORM\Column(name="height", type="smallint", nullable=true, options={"comment":"In millimeters"})
     */
    private $height;

    /**
     * @var int
     *
     * @ORM\Column(name="weight", type="smallint", nullable=true, options={"comment":"In grams"}))
     */
    private $weight;

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @var int
     *
     * @ORM\Column(name="waist_2d", type="smallint", nullable=true, options={"comment":"In millimeters"})
     */
    private $waist2D;

    /**
     * @var int
     *
     * @ORM\Column(name="hip_2d", type="smallint", nullable=true, options={"comment":"In millimeters"}))
     */
    private $hip2D;

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @var int
     *
     * @ORM\Column(name="arm", type="smallint", nullable=true, nullable=true, options={"comment":"In millimeters"}))
     */
    private $arm;

    /**
     * @var int
     *
     * @ORM\Column(name="arm_spine", type="smallint", nullable=true, nullable=true, options={"comment":"In millimeters"}))
     */
    private $armSpine;

    /**
     * @var int
     *
     * @ORM\Column(name="arm_turn", type="smallint", nullable=true, nullable=true, options={"comment":"In millimeters"}))
     */
    private $armTurn;

    /**
     * @var int
     *
     * @ORM\Column(name="bra", type="smallint", nullable=true, nullable=true, options={"comment":"In millimeters"}))
     */
    private $bra;

    /**
     * @var int
     *
     * @ORM\Column(name="chest", type="smallint", nullable=true, nullable=true, options={"comment":"In millimeters"}))
     */
    private $chest;

    /**
     * @var int
     *
     * @ORM\Column(name="calf", type="smallint", nullable=true, nullable=true, options={"comment":"In millimeters"}))
     */
    private $calf;

    /**
     * @var int
     *
     * @ORM\Column(name="shoulder_to_hip", type="smallint", nullable=true, nullable=true, options={"comment":"In millimeters"}))
     */
    private $shoulderToHip;

    /**
     * @var int
     *
     * @ORM\Column(name="hollow_to_floor", type="smallint", nullable=true, nullable=true, options={"comment":"In millimeters"}))
     */
    private $hollowToFloor;

    /**
     * @var int
     *
     * @ORM\Column(name="finger", type="smallint", nullable=true, nullable=true, options={"comment":"In millimeters"}))
     */
    private $finger;

    /**
     * @var int
     *
     * @ORM\Column(name="foot_length", type="smallint", nullable=true, options={"comment":"In millimeters"}))
     */
    private $footLength;

    /**
     * @var int
     *
     * @ORM\Column(name="foot_width", type="smallint", nullable=true, options={"comment":"In millimeters"}))
     */
    private $footWidth;

    /**
     * @var int
     *
     * @ORM\Column(name="hand_length", type="smallint", nullable=true, options={"comment":"In millimeters"}))
     */
    private $handLength;

    /**
     * @var int
     *
     * @ORM\Column(name="hand_width", type="smallint", nullable=true, options={"comment":"In millimeters"}))
     */
    private $handWidth;

    /**
     * @var int
     *
     * @ORM\Column(name="head", type="smallint", nullable=true, options={"comment":"In millimeters"}))
     */
    private $head;

    /**
     * @var int
     *
     * @ORM\Column(name="hip", type="smallint", nullable=true, options={"comment":"In millimeters"}))
     */
    private $hip;

    /**
     * @var int
     *
     * @ORM\Column(name="inside_leg", type="smallint", nullable=true, options={"comment":"In millimeters"}))
     */
    private $insideLeg;

    /**
     * @var int
     *
     * @ORM\Column(name="leg_length", type="smallint", nullable=true, options={"comment":"In millimeters"}))
     */
    private $legLength;

    /**
     * @var int
     *
     * @ORM\Column(name="neck", type="smallint", nullable=true, options={"comment":"In millimeters"}))
     */
    private $neck;

    /**
     * @var int
     *
     * @ORM\Column(name="shoulder", type="smallint", nullable=true, options={"comment":"In millimeters"}))
     */
    private $shoulder;

    /**
     * @var int
     *
     * @ORM\Column(name="thigh", type="smallint", nullable=true, options={"comment":"In millimeters"}))
     */
    private $thigh;

    /**
     * @var int
     *
     * @ORM\Column(name="waist_top", type="smallint", nullable=true, options={"comment":"In millimeters"}))
     */
    private $waistTop;

    /**
     * @var int
     *
     * @ORM\Column(name="waist_bottom", type="smallint", nullable=true, options={"comment":"In millimeters"}))
     */
    private $waistBottom;

    /**
     * @var int
     *
     * @ORM\Column(name="waist", type="smallint", nullable=true, options={"comment":"In millimeters"}))
     */
    private $waist;

    /**
     * @var int
     *
     * @ORM\Column(name="wrist", type="smallint", nullable=true, options={"comment":"In millimeters"}))
     */
    private $wrist;

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @var string
     *
     * @ORM\Column(name="skin", type="string", length=32, nullable=true)
     */
    private $skin;

    /**
     * @var string
     *
     * @ORM\Column(name="hair", type="string", length=16, nullable=true)
     */
    private $hair;

    /**
     * @var string
     *
     * @ORM\Column(name="eyes", type="string", length=16, nullable=true)
     */
    private $eyes;

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @var string
     *
     * @ORM\Column(name="size", type="string", length=8, nullable=true)
     */
    private $size;

    /**
     * @var string
     *
     * @ORM\Column(name="morphotype", type="string", length=15, nullable=true)
     */
    private $morphotype;

    /**
     * @var string
     *
     * @ORM\Column(name="morphoprofile", type="string", length=4, nullable=true)
     */
    private $morphoprofile;

    /**
     * @var string
     *
     * @ORM\Column(name="morpho_weight", type="string", length=8, nullable=true)
     */
    private $morphoWeight;

    /**
     * @param User $user
     *
     * @return PhysicalProfile
     */
    public static function create(User $user): self
    {
        $instance = new self();
        $instance->setUser($user);
        $instance->setCurrent(true);

        return $instance;
    }

    /**
     * Get order display.
     *
     * @return array
     */
    public static function getOrder(): array
    {
        return [
            ['height', 'weight'],
            ['shoulder', 'waist_2d', 'hip_2d', 'chest', 'waist_top', 'waist', 'waist_bottom', 'hip'],
            ['skin', 'eyes', 'hair'],
            ['neck', 'bra', 'arm', 'arm_spine', 'arm_turn', 'wrist', 'shoulder_to_hip', 'hollow_to_floor', 'inside_leg', 'thigh', 'calf'],
            ['head', 'finger', 'hand_length', 'hand_width', 'foot_length', 'foot_width'],
        ];
    }

    /**
     * Get percentage of profile progress.
     *
     * @param int $max
     *
     * @return float
     */
    public function getPercentageOfFilling(int $max = 100): float
    {
        $set = 0;
        $total = 0;
        $orders = self::getOrder();

        foreach ($orders as $step => $datas) {
            foreach ($datas as $elem) {
                if (!is_null($this->{'get'.ucfirst(Str::toCamelCase($elem))}())) {
                    ++$set;
                }
                ++$total;
            }
        }

        if (0 == $total) {
            return floatval(0);
        }
        $total = min($total, $max);

        return round(floatval(100.0 * $set / $total), 0);
    }

    /**
     * MàJ des données en provenvance de l'application mobile.
     *
     * @param array $datas
     *
     * @return PhysicalProfile
     */
    public function setApplicationDatas(array $datas): self
    {
        $weight = $this->getFloatOrNull($datas, 'weight');
        $height = $this->getFloatOrNull($datas, 'height');
        $waist_2d = $this->getFloatOrNull($datas, 'waist_2d');
        $hip_2d = $this->getFloatOrNull($datas, 'hip_2d');
        $arm = $this->getFloatOrNull($datas, 'arm');
        $armSpine = $this->getFloatOrNull($datas, 'arm_spine');
        $armTurn = $this->getFloatOrNull($datas, 'arm_turn');
        $bra = $this->getFloatOrNull($datas, 'bra');
        $chest = $this->getFloatOrNull($datas, 'chest');
        $calf = $this->getFloatOrNull($datas, 'calf');
        $finger = $this->getFloatOrNull($datas, 'finger');
        $foot_length = $this->getFloatOrNull($datas, 'foot_length');
        $foot_width = $this->getFloatOrNull($datas, 'foot_width');
        $hand_length = $this->getFloatOrNull($datas, 'hand_length');
        $hand_width = $this->getFloatOrNull($datas, 'hand_width');
        $head = $this->getFloatOrNull($datas, 'head');
        $hip = $this->getFloatOrNull($datas, 'hip');
        $inside_leg = $this->getFloatOrNull($datas, 'inside_leg');
        $neck = $this->getFloatOrNull($datas, 'neck');
        $shoulder = $this->getFloatOrNull($datas, 'shoulder');
        $thigh = $this->getFloatOrNull($datas, 'thigh');
        //$waist = $this->getFloatOrNull($datas, 'waist');
        //$waist_top = $this->getFloatOrNull($datas, 'waist_top');
        // HACK APPLI
        $waist = $this->getFloatOrNull($datas, 'waist_top');
        $waist_top = $this->getFloatOrNull($datas, 'waist');
        // FIN HACK APPLI

        $waist_bottom = $this->getFloatOrNull($datas, 'waist_bottom');
        $wrist = $this->getFloatOrNull($datas, 'wrist');
        $shoulder_to_hip = $this->getFloatOrNull($datas, 'shoulder_to_hip');
        $hollow_to_floor = $this->getFloatOrNull($datas, 'hollow_to_floor');
        $leg_length = $this->getFloatOrNull($datas, 'leg_length');


        if (!is_null($weight)) {
            $this->setWeight($weight);
        }
        if (!is_null($height)) {
            $this->setHeight($height);
        }
        if (!is_null($waist_2d)) {
            $this->setWaist2D($waist_2d);
        }
        if (!is_null($hip_2d)) {
            $this->setHip2D($hip_2d);
        }
        if (!is_null($arm)) {
            $this->setArm($arm);
        }
        if (!is_null($armSpine)) {
            $this->setArmSpine($armSpine);
        }
        if (!is_null($armTurn)) {
            $this->setArmTurn($armTurn);
        }
        if (!is_null($bra)) {
            $this->setBra($bra);
        }
        if (!is_null($chest)) {
            $this->setChest($chest);
        }
        if (!is_null($calf)) {
            $this->setCalf($calf);
        }
        if (!is_null($finger)) {
            $this->setFinger($finger);
        }
        if (!is_null($foot_length)) {
            $this->setFootLength($foot_length);
        }
        if (!is_null($foot_width)) {
            $this->setFootWidth($foot_width);
        }
        if (!is_null($hand_length)) {
            $this->setHandLength($hand_length);
        }
        if (!is_null($hand_width)) {
            $this->setHandWidth($hand_width);
        }
        if (!is_null($head)) {
            $this->setHead($head);
        }
        if (!is_null($hip)) {
            $this->setHip($hip);
        }
        if (!is_null($inside_leg)) {
            $this->setInsideLeg($inside_leg);
        }
        if (!is_null($neck)) {
            $this->setNeck($neck);
        }
        if (!is_null($shoulder)) {
            $this->setShoulder($shoulder);
        }
        if (!is_null($thigh)) {
            $this->setThigh($thigh);
        }
        if (!is_null($waist)) {
            $this->setWaist($waist);
        }
        if (!is_null($wrist)) {
            $this->setWrist($wrist);
        }
        if (!is_null($shoulder_to_hip)) {
            $this->setShoulderToHip($shoulder_to_hip);
        }
        if (!is_null($hollow_to_floor)) {
            $this->setHollowToFloor($hollow_to_floor);
        }
        if (!is_null($leg_length)) {
            $this->setLegLength($leg_length);
        }
        if (!is_null($waist_top)) {
            $this->setWaistTop($waist_top);
        }
        if (!is_null($waist_bottom)) {
            $this->setWaistBottom($waist_bottom);
        }

        return $this;
    }

    private function getFloatOrNull(array $datas, string $name)
    {
        if (!isset($datas[$name])) {
            return null;
        }

        $val = floatval($datas[$name]) == $datas[$name] ? floatval($datas[$name]) : null;
        if (is_null($val)) {
            return null;
        }

        return max(0, min($val, 5000));
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set current.
     *
     * @param bool $current
     *
     * @return PhysicalProfile
     */
    public function setCurrent($current): self
    {
        $this->current = $current;

        return $this;
    }

    /**
     * Get current.
     *
     * @return bool
     */
    public function getCurrent(): bool
    {
        return $this->current;
    }

    /**
     * Set height.
     *
     * @param int $height
     *
     * @return PhysicalProfile
     */
    public function setHeight($height): self
    {
        $this->height = $this->setMeasure($height);

        return $this;
    }

    /**
     * Get height.
     *
     * @return float
     */
    public function getHeight()
    {
        return $this->getMeasure($this->height);
    }

    /**
     * Set weight.
     *
     * @param int $weight
     *
     * @return PhysicalProfile
     */
    public function setWeight($weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight.
     *
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set waist2D.
     *
     * @param int $waist2D
     *
     * @return PhysicalProfile
     */
    public function setWaist2D($waist2D): self
    {
        $this->waist2D = $this->setMeasure($waist2D);

        return $this;
    }

    /**
     * Get waist2D.
     *
     * @return int
     */
    public function getWaist2D()
    {
        return $this->getMeasure($this->waist2D);
    }

    /**
     * Set hip2D.
     *
     * @param int $hip2D
     *
     * @return PhysicalProfile
     */
    public function setHip2D($hip2D): self
    {
        $this->hip2D = $this->setMeasure($hip2D);

        return $this;
    }

    /**
     * Get hip2D.
     *
     * @return int
     */
    public function getHip2D()
    {
        return $this->getMeasure($this->hip2D);
    }

    /**
     * Set arm.
     *
     * @param int $arm
     *
     * @return PhysicalProfile
     */
    public function setArm($arm): self
    {
        $this->arm = $this->setMeasure($arm);

        return $this;
    }

    /**
     * Get arm.
     *
     * @return int
     */
    public function getArm()
    {
        return $this->getMeasure($this->arm);
    }

    /**
     * Set armSpine.
     *
     * @param int $armSpine
     *
     * @return PhysicalProfile
     */
    public function setArmSpine($armSpine): self
    {
        $this->armSpine = $this->setMeasure($armSpine);

        return $this;
    }

    /**
     * Get armSpine.
     *
     * @return int
     */
    public function getArmSpine()
    {
        return $this->getMeasure($this->armSpine);
    }

    /**
     * Set armTurn.
     *
     * @param int $armTurn
     *
     * @return PhysicalProfile
     */
    public function setArmTurn($armTurn): self
    {
        $this->arm = $this->setMeasure($armTurn);

        return $this;
    }

    /**
     * Get armTurn.
     *
     * @return int
     */
    public function getArmTurn()
    {
        return $this->getMeasure($this->armTurn);
    }

    /**
     * Set bra.
     *
     * @param int $bra
     *
     * @return PhysicalProfile
     */
    public function setBra($bra): self
    {
        $this->bra = $this->setMeasure($bra);

        return $this;
    }

    /**
     * Get bra.
     *
     * @return int
     */
    public function getBra()
    {
        return $this->getMeasure($this->bra);
    }

    /**
     * Set chest.
     *
     * @param int $chest
     *
     * @return PhysicalProfile
     */
    public function setChest($chest): self
    {
        $this->chest = $this->setMeasure($chest);

        return $this;
    }

    /**
     * Get chest.
     *
     * @return int
     */
    public function getChest()
    {
        return $this->getMeasure($this->chest);
    }

    /**
     * Set calf.
     *
     * @param int $calf
     *
     * @return PhysicalProfile
     */
    public function setCalf($calf): self
    {
        $this->calf = $this->setMeasure($calf);

        return $this;
    }

    /**
     * Get calf.
     *
     * @return int
     */
    public function getCalf()
    {
        return $this->getMeasure($this->calf);
    }

    /**
     * Set finger.
     *
     * @param int $finger
     *
     * @return PhysicalProfile
     */
    public function setFinger($finger): self
    {
        $this->finger = $this->setMeasure($finger);

        return $this;
    }

    /**
     * Get finger.
     *
     * @return int
     */
    public function getFinger()
    {
        return $this->getMeasure($this->finger);
    }

    /**
     * Set footLength.
     *
     * @param int $footLength
     *
     * @return PhysicalProfile
     */
    public function setFootLength($footLength): self
    {
        $this->footLength = $this->setMeasure($footLength);

        return $this;
    }

    /**
     * Get footLength.
     *
     * @return int
     */
    public function getFootLength()
    {
        return $this->getMeasure($this->footLength);
    }

    /**
     * Set footWidth.
     *
     * @param int $footWidth
     *
     * @return PhysicalProfile
     */
    public function setFootWidth($footWidth): self
    {
        $this->footWidth = $this->setMeasure($footWidth);

        return $this;
    }

    /**
     * Get footWidth.
     *
     * @return int
     */
    public function getFootWidth()
    {
        return $this->getMeasure($this->footWidth);
    }

    /**
     * Set handLength.
     *
     * @param int $handLength
     *
     * @return PhysicalProfile
     */
    public function setHandLength($handLength): self
    {
        $this->handLength = $this->setMeasure($handLength);

        return $this;
    }

    /**
     * Get handLength.
     *
     * @return int
     */
    public function getHandLength()
    {
        return $this->getMeasure($this->handLength);
    }

    /**
     * Set handWidth.
     *
     * @param int $handWidth
     *
     * @return PhysicalProfile
     */
    public function setHandWidth($handWidth): self
    {
        $this->handWidth = $this->setMeasure($handWidth);

        return $this;
    }

    /**
     * Get handWidth.
     *
     * @return int
     */
    public function getHandWidth()
    {
        return $this->getMeasure($this->handWidth);
    }

    /**
     * Set head.
     *
     * @param int $head
     *
     * @return PhysicalProfile
     */
    public function setHead($head): self
    {
        $this->head = $this->setMeasure($head);

        return $this;
    }

    /**
     * Get head.
     *
     * @return int
     */
    public function getHead()
    {
        return $this->getMeasure($this->head);
    }

    /**
     * Set hip.
     *
     * @param int $hip
     *
     * @return PhysicalProfile
     */
    public function setHip($hip): self
    {
        $this->hip = $this->setMeasure($hip);

        return $this;
    }

    /**
     * Get hip.
     *
     * @return int
     */
    public function getHip()
    {
        return $this->getMeasure($this->hip);
    }

    /**
     * Set insideLeg.
     *
     * @param int $insideLeg
     *
     * @return PhysicalProfile
     */
    public function setInsideLeg($insideLeg): self
    {
        $this->insideLeg = $this->setMeasure($insideLeg);

        return $this;
    }

    /**
     * Get insideLeg.
     *
     * @return int
     */
    public function getInsideLeg()
    {
        return $this->getMeasure($this->insideLeg);
    }

    /**
     * Set neck.
     *
     * @param int $neck
     *
     * @return PhysicalProfile
     */
    public function setNeck($neck): self
    {
        $this->neck = $this->setMeasure($neck);

        return $this;
    }

    /**
     * Get neck.
     *
     * @return int
     */
    public function getNeck()
    {
        return $this->getMeasure($this->neck);
    }

    /**
     * Set shoulder.
     *
     * @param int $shoulder
     *
     * @return PhysicalProfile
     */
    public function setShoulder($shoulder): self
    {
        $this->shoulder = $this->setMeasure($shoulder);

        return $this;
    }

    /**
     * Get shoulder.
     *
     * @return int
     */
    public function getShoulder()
    {
        return $this->getMeasure($this->shoulder);
    }

    /**
     * Set thigh.
     *
     * @param int $thigh
     *
     * @return PhysicalProfile
     */
    public function setThigh($thigh): self
    {
        $this->thigh = $this->setMeasure($thigh);

        return $this;
    }

    /**
     * Get thigh.
     *
     * @return int
     */
    public function getThigh()
    {
        return $this->getMeasure($this->thigh);
    }

    /**
     * Set waist.
     *
     * @param int $waist
     *
     * @return PhysicalProfile
     */
    public function setWaist($waist): self
    {
        $this->waist = $this->setMeasure($waist);

        return $this;
    }

    /**
     * Get waist.
     *
     * @return int
     */
    public function getWaist()
    {
        return $this->getMeasure($this->waist);
    }

    /**
     * @return int
     */
    public function getLegLength()
    {
        return $this->getMeasure($this->legLength);
    }

    /**
     * @param int $legLength
     *
     * @return PhysicalProfile
     */
    public function setLegLength(int $legLength): self
    {
        $this->legLength = $this->setMeasure($legLength);

        return $this;
    }

    /**
     * @return float|null
     */
    public function getWaistTop()
    {
        return  $this->getMeasure($this->waistTop);
    }

    /**
     * @param int $waistTop
     *
     * @return PhysicalProfile
     */
    public function setWaistTop(int $waistTop): self
    {
        $this->waistTop = $this->setMeasure($waistTop);

        return $this;
    }

    /**
     * @return int
     */
    public function getWaistBottom()
    {
        return $this->getMeasure($this->waistBottom);
    }

    /**
     * @param int $waistBottom
     *
     * @return PhysicalProfile
     */
    public function setWaistBottom(int $waistBottom): self
    {
        $this->waistBottom = $this->setMeasure($waistBottom);

        return $this;
    }

    /**
     * Set wrist.
     *
     * @param int $wrist
     *
     * @return PhysicalProfile
     */
    public function setWrist($wrist): self
    {
        $this->wrist = $this->setMeasure($wrist);

        return $this;
    }

    /**
     * Get wrist.
     *
     * @return int
     */
    public function getWrist()
    {
        return $this->getMeasure($this->wrist);
    }

    /**
     * Set shoulderToHip.
     *
     * @param int $shoulderToHip
     *
     * @return PhysicalProfile
     */
    public function setShoulderToHip($shoulderToHip): self
    {
        $this->shoulderToHip = $this->setMeasure($shoulderToHip);

        return $this;
    }

    /**
     * Get shoulderToHip.
     *
     * @return int
     */
    public function getShoulderToHip()
    {
        return $this->getMeasure($this->shoulderToHip);
    }

    /**
     * Set hollowToFloor.
     *
     * @param int $hollowToFloor
     *
     * @return PhysicalProfile
     */
    public function setHollowToFloor($hollowToFloor): self
    {
        $this->hollowToFloor = $this->setMeasure($hollowToFloor);

        return $this;
    }

    /**
     * Get hollowToFloor.
     *
     * @return int
     */
    public function getHollowToFloor()
    {
        return $this->getMeasure($this->hollowToFloor);
    }

    /**
     * Set skin.
     *
     * @param string $skin
     *
     * @return PhysicalProfile
     */
    public function setSkin($skin): self
    {
        $this->skin = $skin;

        return $this;
    }

    /**
     * Get skin.
     *
     * @return string
     */
    public function getSkin()
    {
        return $this->skin;
    }

    /**
     * Set hair.
     *
     * @param string $hair
     *
     * @return PhysicalProfile
     */
    public function setHair($hair): self
    {
        $this->hair = $hair;

        return $this;
    }

    /**
     * Get hair.
     *
     * @return string
     */
    public function getHair()
    {
        return $this->hair;
    }

    /**
     * Set eyes.
     *
     * @param string $eyes
     *
     * @return PhysicalProfile
     */
    public function setEyes($eyes): self
    {
        $this->eyes = $eyes;

        return $this;
    }

    /**
     * Get eyes.
     *
     * @return string
     */
    public function getEyes()
    {
        return $this->eyes;
    }

    /**
     * Set size.
     *
     * @param string $size
     *
     * @return PhysicalProfile
     */
    public function setSize($size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size.
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set morphotype.
     *
     * @param string $morphotype
     *
     * @return PhysicalProfile
     */
    public function setMorphotype($morphotype): self
    {
        $this->morphotype = $morphotype;

        return $this;
    }

    /**
     * Get morphotype.
     *
     * @return string
     */
    public function getMorphotype()
    {
        return $this->morphotype;
    }

    /**
     * Set morphoprofile.
     *
     * @param string $morphoprofile
     *
     * @return PhysicalProfile
     */
    public function setMorphoprofile($morphoprofile): self
    {
        $this->morphoprofile = $morphoprofile;

        return $this;
    }

    /**
     * Get morphoprofile.
     *
     * @return string
     */
    public function getMorphoprofile()
    {
        return $this->morphoprofile;
    }

    /**
     * Set morphoWeight.
     *
     * @param string $morphoWeight
     *
     * @return PhysicalProfile
     */
    public function setMorphoWeight($morphoWeight): self
    {
        $this->morphoWeight = $morphoWeight;

        return $this;
    }

    /**
     * Get morphoWeight.
     *
     * @return string
     */
    public function getMorphoWeight()
    {
        return $this->morphoWeight;
    }

    /**
     * Set user.
     *
     * @param User $user
     *
     * @return PhysicalProfile
     */
    public function setUser(User $user = null): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Getter Helper.
     *
     * @param string $key
     *
     * @return mixed
     *
     * @throws NullException
     */
    public function get(string $key)
    {
        $key = Str::toCamelCase($key);
        try {
            return $this->{'get'.ucfirst($key)}();
        } catch (\Exception $exception) {
            throw new NullException('Key '.$key.' is unknown in Entity Physical Profile');
        }
    }

    /**
     * Setter Helper.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return PhysicalProfile
     *
     * @throws NullException
     */
    public function set(string $key, $value): self
    {
        try {
            $key = Str::toCamelCase($key);

            return $this->{'set'.ucfirst($key)}($value);
        } catch (\Exception $exception) {
            throw new NullException('Key '.$key.' is unknown in Entity Physical Profile');
        }
    }

    /**
     * @return PhysicalProfileDTO
     */
    public function getDTO(): PhysicalProfileDTO
    {
        $dto = new PhysicalProfileDTO([]);
        $dto->id = $this->getId();
        $dto->userId = $this->getUser()->getId();
        $dto->current = $this->getCurrent();
        $dto->gender = $this->getUser()->getGender();
        $dto->height = $this->height;
        $dto->weight = $this->weight;
        $dto->arm = $this->arm;
        $dto->armSpine = $this->armSpine;
        $dto->armTurn = $this->armTurn;
        $dto->bra = $this->bra;
        $dto->chest = $this->chest;
        $dto->calf = $this->calf;
        $dto->finger = $this->finger;
        $dto->footLength = $this->footLength;
        $dto->footWidth = $this->footWidth;
        $dto->handLength = $this->handLength;
        $dto->handWidth = $this->handWidth;
        $dto->hollowToFloor = $this->hollowToFloor;
        $dto->head = $this->head;
        $dto->hip = $this->hip;
        $dto->insideLeg = $this->insideLeg;
        $dto->neck = $this->neck;
        $dto->shoulder = $this->shoulder;
        $dto->shoulderToHip = $this->shoulderToHip;
        $dto->thigh = $this->thigh;
        $dto->waist = $this->waist;
        $dto->waistTop = $this->waistTop;
        $dto->waistBottom = $this->waistBottom;
        $dto->wrist = $this->wrist;
        $dto->skin = $this->getSkin();
        $dto->hair = $this->getHair();
        $dto->eyes = $this->getEyes();
        $dto->size = $this->getSize();
        $dto->morphotype = $this->getMorphotype();
        $dto->morphoprofile = $this->getMorphoprofile();
        $dto->morphoWeight = $this->getMorphoWeight();

        return $dto;
    }

    /**
     * Return true if required value are not null.
     *
     * @return bool
     */
    public function checkRequiredValue(): bool
    {
        return !is_null($this->morphoWeight) && !is_null($this->morphoprofile) && !is_null($this->morphotype);
    }

    /**
     * cm => mm (if $coef = 10).
     *
     * @param     $measure
     * @param int $coefficient
     *
     * @return null|int
     */
    private function setMeasure($measure, int $coefficient = 10): ?int
    {
        if (is_null($measure)) {
            return $measure;
        }

        return intval($coefficient * floatval($measure));
    }

    /**
     * mm => cm.
     *
     * @param int $measure
     * @param int $coefficient
     *
     * @return float|null
     */
    private function getMeasure($measure, int $coefficient = 10)
    {
        if (is_null($measure)) {
            return $measure;
        }

        return round((1.0 * $measure) / $coefficient, 1);
    }
}
