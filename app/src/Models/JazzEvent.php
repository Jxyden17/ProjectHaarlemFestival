<?php

namespace App\Models;



class JazzEvent
{
    private int $id;
    private string $artistId;
    private string $artistName;
    private string $time;
    private string $location;
    private int $freeSeats;
    private int $totalSeats;
    private float $price;
    private string $photoUrl;
    private string $day;

    public function __construct(int $id, string $artistId, string $artistName, string $time, string $location,int $freeSeats,int $totalSeats,float $price,string $photoUrl,string $day)
    {
        $this->id = $id;
        $this->artistId = $artistId;
        $this->artistName = $artistName;
        $this->time = $time;
        $this->location = $location;
        $this->freeSeats = $freeSeats;
        $this->totalSeats = $totalSeats;
        $this->price = $price;
        $this->photoUrl = $photoUrl;
        $this->day = $day;

    }

    public function getId() {
        return $this->id;
    }

    public function setId(int $id) {
       $this->id = $id;
    }

    public function getArtistId() {
        return $this->artistId;
    }

    public function setArtistId(string $artistId) {
        $this->artistId = $artistId;
    }

    public function getArtistName() {
        return $this->artistName;
    }

    public function setArtistName(string $artistName) {
        $this->artistName = $artistName;
    }

    public function getLocation() {
        return $this->location;
    }

    public function setLocation(string $location) {
        $this->location = $location;
    }

    public function getTotalSeats() {
        return $this->totalSeats;
    }

    public function setTotalSeats(int $totalSeats) {
        $this->totalSeats = $totalSeats;
    }

    public function getPrice() {
        return $this->price;
    }

    public function setPrice(float $price) {
        $this->price = $price;
    }

    public function getPhotoUrl() {
        return $this->photoUrl;
    }

    public function setPhotoUrl(string $photoUrl) {
        $this->photoUrl = $photoUrl;
    }

    public function getFreeSeats() {
        return $this->freeSeats;
    }

    public function setFreeSeats(int $freeSeats) {
        $this->freeSeats = $freeSeats;
    }

    public function getTime() {
        return $this->time;
    }

    public function setTime(string $time) {
        $this->time = $time;
    }

    public function getDay() {
        return $this->day;
    }

    public function setDay(string $day) {
       $this->day = $day;
    }


    
}